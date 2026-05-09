## 2024-05-09 - XSS Vulnerability in Email Template
**Vulnerability:** XSS vulnerability found in `resources/views/emails/domain_warning.blade.php`.
**Learning:** The template used `{!! $issue !!}` to render an array of issues. The issues array is populated from parsed HTML of the crawled URL. If an attacker controls the title or content of the website, they can insert HTML tags (e.g. `<script>alert(1)</script>`) into the issues. This would lead to a Stored XSS via email since the email is sent to the domain owner with the unescaped script tag.
**Prevention:** Always use `{{ $issue }}` in Blade templates for data coming from user inputs, including data scraped from the web. Only use `{!! !!}` if the content is explicitly sanitized and trusted.
