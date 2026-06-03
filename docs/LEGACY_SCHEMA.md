# Legacy Database Tables

Early Spectora SaaS migrations created tables that are **not used** by the self-hosted edition:

- `plans`, `modules`, `subscriptions`, `stripe_events`
- `personal_access_tokens` (Sanctum — installed but no API routes)

They remain in the schema for backward compatibility on upgraded instances. New installs may create empty tables; they can be ignored.

A future major release may drop these via an optional migration.
