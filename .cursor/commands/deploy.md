---
name: deploy
description: Review the local diff, block on must-fix or should-fix findings, then build, push, and deploy the MESSCUT theme to Hosting Ukraine.
---

# /deploy — review, build, push, host

Ship the MESSCUT theme only after a local-diff review with **zero must-fix and zero should-fix** findings. Then build CSS, commit/push, and upload to Hosting Ukraine.

This command **is** explicit intent to commit relevant files, push, and deploy. Do not ask for confirmation of those steps. Stop immediately on a blocking finding, a failed build, a failed push, or a failed deploy.

## Hard rules

- Never commit or push secrets: `.env.deploy`, `.env.local`, credentials, keys.
- Never deploy WordPress core, plugins, `uploads/`, `wp-config.php`, or the local database. Hosting upload is **theme only** via `./scripts/deploy.sh`.
- Never run `inc/seed*.php` on production.
- Never force-push to `main` / `master`. Never `--force` / `--force-with-lease` unless the user explicitly asks in this turn.
- Never skip hooks (`--no-verify`).
- Never amend unless the user asked and amend safety rules allow it.
- Do not hand-edit `assets/css/main.css`; the build step compiles it from SCSS.

## Exclude from git (never stage / never push)

- `.env.deploy`, `.env.local`, `.env.*.local`
- `wordpress/`, `wp-content/plugins/`, `wp-content/uploads/`, `node_modules/`
- `Project Requirements.md`, `.DS_Store`, `*.log`
- Unrelated WIP the user did not ask to ship

If a secret or trash file is already staged, unstage it and warn before continuing.

## Severity (review gate)

| Level | Meaning | Action |
|-------|---------|--------|
| **Must fix** | Bugs, security, XSS/escaping gaps, secret leak, broken templates, data loss | **STOP — do not build, push, or deploy** |
| **Should fix** | Clear correctness/quality issues that should not ship (a11y breakage, broken i18n, obvious regressions, missing escaping on new output) | **STOP — do not build, push, or deploy** |
| Nice to have | Nits, optional polish, non-blocking suggestions | Report only; continue |

Map Bugbot/security severities: Critical / High / Medium → **Must fix** or **Should fix** (both block). Low / Note / Nit → Nice to have (do not block). If a finding is labeled must-fix or should-fix, it blocks regardless of tool wording.

**On any Must fix or Should fix → STOP.** Print the blocked report. Do not continue to build, commit, push, or `./scripts/deploy.sh`.

## Workflow (execute in order)

Copy and track progress:

```
/deploy progress:
- [ ] 1. Inspect repo + local diff
- [ ] 2. Code review (block on must/should fix)
- [ ] 3. Build
- [ ] 4. Commit + push all relevant changes
- [ ] 5. Deploy to Hosting Ukraine
- [ ] 6. Report
```

### 1. Inspect repo + local diff

From the repo root, run in parallel:

```bash
git status
git diff && git diff --cached
git log -5 --oneline
git branch -vv
git rev-parse --abbrev-ref HEAD
```

Note: branch, uncommitted/untracked files, whether HEAD is ahead of upstream.

If not a git repo or detached HEAD with no clear branch — **stop** and report.

### 2. Code review

Review the **local diff** that will ship (working tree + index; if the tree is clean, review unpushed commits / `branch changes`).

Launch **exactly one** `bugbot` subagent (`run_in_background: false`, `description: "Bugbot"`) with this prompt shape (do not compute the diff yourself first):

```text
Full Repository Path: <absolute repository path>
Diff: uncommitted changes
Custom Instructions: Blockers are must-fix and should-fix only. Flag WordPress escaping gaps (esc_html/esc_url/esc_attr), secret leaks, XSS, broken templates, and theme regressions. Ignore nits unless they are should-fix.
```

If the working tree is clean (nothing uncommitted) but there are unpushed commits, use `Diff: branch changes` instead of `uncommitted changes`.

If Bugbot cannot complete after one retry, do an inline review of the same local diff against `CLAUDE.md` (escaping, enqueue, i18n, no seed-on-prod, theme-only deploy). Do **not** fail open.

Also reject the diff if it stages `.env.deploy` or other secrets.

Print a compact table of findings (Severity, Location, Finding). Then apply the severity gate above.

### 3. Build

Only if the review passed:

```bash
cd wp-content/themes/messcut && npm run build
```

**On failure → STOP.** Do not push or deploy.

If `assets/css/main.css` changed, it must be included in the commit in step 4.

### 4. Commit + push all relevant changes

Include source, compiled CSS, scripts, and docs that belong to this ship. Exclude the exclude list.

If there are relevant uncommitted changes:

1. Stage allowed files only.
2. Commit with a conventional message via HEREDOC (`feat` / `fix` / `chore` / …). Focus on why.
3. `git status` after the commit to verify.

Then push:

```bash
git push -u origin HEAD
```

If upstream already tracks, `git push` is enough. Request permissions if git network is blocked.

If there is **no remote**, skip push, say so, and continue to deploy.

**On push failure (remote exists) → STOP.** Do not deploy.

### 5. Deploy to Hosting Ukraine

```bash
./scripts/deploy.sh
```

Requires `.env.deploy` (gitignored). Theme-only tar-over-SSH; do not replace this with rsync from macOS.

**On failure → STOP** and include the script output in the report.

After CPT/rewrite template changes, remind: WP admin → Settings → Permalinks → Save.

### 6. Report

Lead with **SUCCESS** or **BLOCKED**.

#### Success

```markdown
## /deploy report — SUCCESS

- Review: no must-fix / should-fix
- Build: ✅
- Commit: `<sha>` `<message>` (or "none — already committed")
- Pushed: yes → `<remote>/<branch>` (or "skipped — no remote")
- Hosting: theme uploaded via `./scripts/deploy.sh`
- Skipped files: <list or "none">
```

#### Blocked

```markdown
## /deploy report — BLOCKED

- Stopped at: <step name>
- Must fix / should fix:
  1. <issue> — <file:line> — <why it blocks>
- Nice to have (non-blocking): ...
- Not pushed / not deployed
- Next actions:
  1. <concrete fix>
  2. Re-run /deploy after fix
```
