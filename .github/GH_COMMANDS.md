# GitHub CLI (`gh`) — Spectora Cheat Sheet

Repo: **Everlite/Spectora-Website-Monitoring-Suite**  
Arbeitsverzeichnis: `spectora-app/` (Git-Root)

Voraussetzung: `gh auth login` (einmalig) → `gh auth status` muss ✓ zeigen.

---

## Repo & About

```bash
cd /path/to/spectora-app

# Aktuelle Repo-Infos
gh repo view
gh repo view --web

# About-Beschreibung (wie README / REPO_DESCRIPTION.txt)
gh repo edit --description "$(head -1 .github/REPO_DESCRIPTION.txt)"

# Remote im Browser öffnen
gh browse
gh browse --settings    # Repo-Einstellungen
```

---

## Git-Stand & Branches

```bash
git status
git pull origin main

# Branch pushen (erstes Mal)
git push -u origin HEAD

# Anderen Branch anlegen
git checkout -b feature/mein-feature
git push -u origin HEAD
```

`gh` ersetzt **nicht** `git push` — nur Metadaten, PRs, Releases, CI.

---

## CI (GitHub Actions)

```bash
# Letzte Workflow-Runs
gh run list --limit 10

# Nur fehlgeschlagene
gh run list --status failure --limit 5

# Live-Log des letzten Runs
gh run watch

# Bestimmten Run (ID aus `gh run list`)
gh run view <RUN_ID>
gh run view <RUN_ID> --log-failed

# Run im Browser
gh run view <RUN_ID> --web

# Workflow erneut starten (z. B. nach Fix)
gh run rerun <RUN_ID>
```

Workflow-Datei: `.github/workflows/tests.yml` (`composer test`).

---

## Pull Requests

```bash
# PR aus aktuellem Branch erstellen
gh pr create --title "Kurzer Titel" --body "$(cat <<'EOF'
## Summary
- …

## Test plan
- [ ] `composer test` lokal oder CI grün
EOF
)"

# PRs listen / ansehen
gh pr list
gh pr view
gh pr view --web
gh pr checks          # CI-Status am PR

# PR mergen (wenn bereit)
gh pr merge --squash --delete-branch
```

---

## Releases & Tags

Vor einem Release: [`docs/STAGING_CHECKLIST.md`](../docs/STAGING_CHECKLIST.md), `CHANGELOG.md` `[Unreleased]` → Version eintragen.

```bash
# Tags anzeigen
git fetch --tags
git tag -l 'v*'

# Tag lokal + remote (Beispiel v0.2.1)
git tag -a v0.2.1 -m "v0.2.1"
git push origin v0.2.1

# GitHub Release mit Notizen (aus CHANGELOG-Abschnitt oder Datei)
gh release create v0.2.1 \
  --title "v0.2.1" \
  --notes-file .github/RELEASE_v0.2.0.md

# Oder kurze Notiz inline
gh release create v0.2.1 --title "v0.2.1" --notes "Siehe CHANGELOG.md"

# Releases listen / öffnen
gh release list
gh release view v0.2.1 --web

# Bestehendes Release bearbeiten
gh release edit v0.2.1 --notes-file CHANGELOG.md
```

---

## Issues (optional)

```bash
gh issue list
gh issue create --title "Bug: …" --body "…"
gh issue view 42 --web
```

---

## Nützliche Aliase (optional, in `~/.bashrc` / `~/.zshrc`)

```bash
alias ghst='gh auth status'
alias ghrun='gh run list --limit 5'
alias ghpr='gh pr view --web'
```

---

## Typischer Ablauf „Release heute“

```bash
cd spectora-app
git pull origin main
gh run list --limit 3                    # CI grün?
# CHANGELOG + ggf. Tag vorbereiten
git add CHANGELOG.md && git commit -m "chore: release v0.2.1"
git push origin main
git tag -a v0.2.1 -m "v0.2.1"
git push origin v0.2.1
gh release create v0.2.1 --title "v0.2.1" --notes "…"
gh repo view --web                       # About + Release prüfen
```

---

## Hilfe

```bash
gh help
gh pr create --help
gh release create --help
```
