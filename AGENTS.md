# AI Team Rules

> DatalabAcademy (PHP / XAMPP / MySQL) layihəsi. Codex əsas builder-dir;
> Codex müstəqil review edir; Gemini uzun bağlamı oxuyur.

## Default role
You are the primary builder. Inspect the repository, make the smallest safe change,
run relevant tests, and explain the final diff.

## Review handoff: Codex
After a feature, bug fix, refactor, or security-sensitive change:
1. Use the codex-review MCP tool.
2. Ask for a read-only review of the current Git diff.
3. Request concrete findings ranked by severity.
4. Do not apply a suggestion until it is verified in the repository.

## Long-context handoff: Gemini
Use Gemini for long PDFs, broad documentation, large logs, or repository-wide questions.
Never include .env files, credentials, tokens, customer data, or private keys.
If Gemini automation is not configured, prepare the exact command and ask me to run it.

## Handoff output
For every handoff, return: what was checked, findings, recommended next action,
and whether a code change is actually needed.

## Codex review prompt (standard)
Review the current Git diff as an independent reviewer. Do not modify files.
Focus on correctness, regression risk, security, edge cases, test gaps, and maintainability.
Return only:
1. Severity: blocker / high / medium / low
2. File and line, or the affected behavior
3. Why it matters
4. The smallest safe fix
If there are no material issues, write: No material issues found.

## Project notes
- Stack: PHP 8.2, XAMPP/Apache, MySQL (PDO). Admin panel: admin.php + api/admin.php.
- Website AI chatbot uses Anthropic (api/ai-client.php); secrets live in api/config.local.php (never commit keys).
- Do not put DL_AI_KEY, API keys, or .env contents into prompts, AGENTS.md, or Git commits.

## Imported Claude Cowork project instructions
