# Smart Mosque System — End-to-End Workflow Documentation

This document explains **how the system actually works**, narrated flow by flow. Each flow is a complete journey that moves sequentially between the people involved — the **Admin (Pentadbir Masjid)**, the **Treasurer (Bendahari)**, and the **Member (Jemaah)** — exactly as it happens in real life, in one continuous story. Where a flow only involves one or two of them, you'll see only those people appear.

The three roles and how accounts are created are covered first, because every flow depends on how the people get into the system.

---

## 0. Roles, accounts & how people enter the system

### The three roles

- **Admin (Administrator)** — `admin@mosque.com`
- **Treasurer (Bendahari)** — `treasurer@mosque.com`
- **Member (Jemaah / Volunteer)** — e.g. `ali@mosque.com`

Every route in the application is guarded by a role check (`CheckRole` middleware). Which user may do what is defined in `config/roles.php` and enforced by `routes/web.php`:

- **Admin only:** record donations (single/batch/bulk), manage events & attendance, create withdrawal requests, manage gamification (badges/rewards/tiers/points), manage amils, regenerate registration codes, manage fund purposes.
- **Treasurer only:** verify (confirm/dispute) donations, approve/reject withdrawals (including the second check on high-value ones), view reports.
- **Admin + Treasurer:** view the donation list, print akad/receipts, view and export reports, view withdrawals list.
- **Member:** dashboard, join/leave volunteer events, manage own profile & volunteer profile, refer friends, participate in gamification (earn points, badges, redeem rewards).

### The registration flow (end-to-end)

**1. The Admin prepares entry codes.**
The Admin goes to **Admin → Settings** (`/admin/settings`) and generates two secret registration codes — an **Admin code** and a **Treasurer code** — using the "Regenerate" buttons. Each click produces a new code (e.g. `ADMIN-X8K2F3QZ` and `TRSR-P4M7W2NV`) stored in the `settings` table. These codes are the only way a trusted person can register as a staff/committee role; everyone else registers as a regular Member. If no code is customised yet, the system falls back to the values in `config/roles.php`.

**2. A trusted contributor registers.**

- On the public registration page (`/register`), the person fills in name, email, phone and password. Inside the "Special Code" field they enter the Admin or Treasurer code they were given.
- The `AuthController@register` method reads that code and asks `RegistrationCodeService` to translate it into a role. If the code is valid → the account is created with `role = admin` or `role = treasurer`. If the code is invalid/blank → the account is created as `role = member` (the normal case for jemaah).
- If the person also entered a **referral code** belonging to a member, the referrer gets 15 bonus points (see Flow E).

**3. Every new account must verify email.**

- The system sends an email verification link. Admins and treasurers must verify too, but once **Admin or Treasurer** logs in, the system silently treats their email as verified.
- A **Member** (`role = member`) is blocked from logging in until they click their verification link — if not verified, the login attempt is rejected with a message to check the inbox. A slower path also exists to resend the link.

**4. The Admin can adjust later.**

- The Admin can later appoint/remove **Amils** (zakat collectors) from `Admin → Amils` (`/admin/amils`) — see Flow A.
- The Admin can regenerate the registration codes whenever they leak or rotate.

---

## Flow A — Fund Intake (Recording & verifying donations)

This flow involves **Admin → Treasurer**, with occasional input from a nominated **Amil**. This is the "money in" side of the treasury.

### A1. Admin records a donation

**1. The Admin opens the Donations record form.**
There are three recording modes available from the donations area:

- **Single entry** (`/donations` → record) — one donation at a time.
- **Batch entry** (`/donations/batch`) — record several **Sadaqah** donations at once, e.g. from the donation box after Friday prayers. Each row is a description + amount + source + date + optional fund purpose.
- **Bulk entry** (`/donations/bulk`) — a single, already-counted "collection-tin" total (Kutipan Pukal): one amount, one fund purpose, notes including optional witness names, recorded directly as **confirmed**.

Because sadaqah is normally anonymous, the system intentionally **strips donor identity** (name/IC/phone/email/address are stored as null) for `sadaqah` entries — the person stays anonymous on the receipt side. For **zakat, zakat fitr and waqf**, the Admin fills donor details (name, IC, phone, email, address) plus a fund purpose and optionally a nominated amil.

**2. The Admin enters the details.** For each entry the form captures:

- **Category** → mapped to a type: `zakat`/`zakat_fitr` → `obligatory`, `waqf` → `endowment`, `sadaqah` → `voluntary`.
- Amount, source (`cash` or `online`), donation date, reference, description, fund purpose.
- Status: normally left `pending`, except the bulk corpus entry which is born `confirmed`.

**3. The system mints a receipt number and (if needed) an akad.**

- `ReceiptNumberService` issues a **sequential receipt number** per year under the `DON` prefix (e.g. `DON-2026-00034`) using a locked sequence row (`receipt_number_sequences`). This number exists from the moment of recording — it is not conditional on confirmation.
- For **zakat / zakat fitr**, the Admin also gave an **amil name**. The system creates a **zakat akad** record (`zakat_akads`) with its own reference (`ZKT-2026-...`), containing the muzakki (payer) name/IC, amil, amount, akad date and notes. This is the formal Islamic agreement for the obligatory charity.

**4. The system rolls forward.** The new donation starts life with status `pending`, linked to the Admin as `created_by`. The donation is instantly visible in the Donations list to **both** Admin and Treasurer, but it is **not yet counted as spendable money** and **no one can print a receipt/akad yet** in a tax-meaningful way — only pending → confirmed unlocks that.

**5. The Treasurer is notified.** The Admin's record action automatically sends a **Donation Notification** to **every treasurer** ("a new donation has been recorded and awaits your verification").

### A2. The Treasurer (Bendahari) verifies

**6. The Treasurer** opens their Donations list (`/donations`), sees the pending donation together with a quick summary (count, total, cash/online split, and a running total for zakat, zakat fitr, sadaqah, waqf). They open the record.

**7. The Treasurer confirms it.**

- Status `pending` → **confirmed**, with `verified_by` (treasurer id) and `verified_at` (timestamp) recorded.
- **Crucially, the Admin cannot confirm their own entry** — the system blocks anyone from confirming a donation they themselves recorded (prevents self-verification / "you can't be your own check").
- The donating member (if known) is notified that their contribution was confirmed.

**8. Or the Treasurer disputes it.**

- If the amount/source is wrong, the Treasurer marks it **disputed** and provides a `rejection_reason`.
- The member is notified of the dispute. A disputed donation does **not** count toward spendable balance.

**9. Printing documents (twin artifacts).**

- Once confirmed, the Admin or Treasurer opens the record and prints the downloadable artefacts:
    - The **akad PDF** (`printAkad`) for zakat/zakat-fit entries, which fails with a message if the entry somehow has no akad yet; and
    - The **receipt PDF** (`printReceipt`) for every donation with a receipt number.

**When is money "available" for spending?** Only **confirmed** donations count toward the spendable balance used by the withdrawal flow (Flow B). Pending and disputed ones are excluded.

### A3. Fund purpose administration (Admin only, ongoing)

- **Fund purposes** (`/donations/fund-purposes`) are managed by the Admin: add, rename, delete, and toggle active/inactive. Each donation and each withdrawal is attached to a fund purpose, and the balances on the withdrawal screen are broken out by purpose.

---

## Flow B — Fund Disbursement (Withdrawals — Maker & Checker)

This is **Admin → Treasurer(s)** and is the strictest flow in the system, because it spends the mosque's money. It is a classic **maker‑checker** process with an extra, mandatory **two-treasurer** layer for high-value payouts.

The withdrawal form checks balance in **two layers**: against the whole fund type, AND against the specific fund purpose, subtracting out money already committed (pending + checked + approved), so the same RM cannot be spent twice.

### B1. Admin creates the withdrawal request (the "Maker")

**1. Admin** opens the Withdrawals area, picks:

- `type` = zakat / zakat_fitr / sadaqah / waqf
- `fund_purpose`
- `amount` and `purpose` (narrative).

**2. Balance check.** The system computes for that type:
`Available = sum(all confirmed donations of that type) − sum(all non-rejected withdrawals of that type)`.
It also checks the same against the chosen fund purpose specifically. If the requested amount exceeds either, the request is rejected with a clear "insufficient balance — available RM X" message and the form is re-shown.

**3. The request is created** with `status = pending`, **requested_by = the Admin**, and `maker_checked_by` null. Any **supporting documents (invoices)** the Admin uploads are stored under `withdrawals/{id}/invoices` as the "maker" side.

**4. Every Treasurer is notified** that a new withdrawal request awaits their approval. (The notifier is `WithdrawalRequestNotification`, step `created`.)

### B2. A Treasurer approves (the "Checker")

**5. A treasurer** opens `/withdrawals`, sees the pending list (counts for pending, checked, approved, rejected), and the per-type/per-purpose running ledger so they can sanity-check the maths.

**6. Decision.**

- Any treasurer can **reject** at any stage before final approval, recording a `rejection_reason`.
- Or the treasurer clicks **Approve**. The system always refuses to let anyone approve a request they created themselves.

**Now the amount decides how many people are needed:**

**Case 1 — amount ≤ RM 1,000 (single check).**
The Treasurer (through `WithdrawalController@approve`) re-validates balance, then immediately moves the request to **status = approved**, records `approved_by`, `approved_at`, and any "proof" documents uploaded to the `proofs` side. The requester (the Admin) is notified "approved". The money is now counted as **spent** on the dashboard and stops being reserveable.

**Case 2 — amount > RM 1,000 (dual / maker‑checker).**

- The first Treasurer's approve **does not finalise** it. The status becomes `maker_checked`, `maker_checked_by` is set, and the system **notifies the OTHER treasurers** (all treasurers except the one who just approved) that this request needs a **second** check. The requester is also told it is "under first check".
- **A different treasurer** (never the same one who did the first check) must approve again. On this second approval the system:
    1. **re-validates the balance again** at the moment of approval (row-locked via `DB::transaction` + `lockForUpdate`, so two people approving at once cannot over-spend),
    2. moves it to `approved` with `approved_by`, `approved_at`, and
    3. notifies the requester "fully approved".
- The **maker‑checker threshold** is enforced by the `WithdrawalRequest::needsMakerChecker()` method, which returns true when `amount > 1000`.

### B3 — Records & proof

- Approving treasurers can attach their own `proof` documents under `documents/proofs/` (receipt of transfer, bank slip, etc.) right at the time of approval.
- Rejected requests (`rejected`) are excluded from the quotas and can be re-submitted.
- Approved withdrawals flow down the **Transparency** page to the member (Flow F).

---

### Flow C — Volunteering & Events

This flow crosses **Admin → Member → Admin** (attendance, then points), with the Treasurer able to view event/attendance reports.

### C1. Admin creates and publishes an event

1. **Admin** opens Events management (`/events/manage`), clicks create. They set title, description, date, location, max volunteers, required skills / hobbies / languages, availability requirements, and a **gamification category** (e.g. `religious`, `education`, `emergency`, etc.).

2. On save the event is created with **status = `open`**. Every Member is sent an **event notification**.

3. Admin can subsequently edit (only before the event date passes), change status (`open` / `closed` / `cancelled`), or delete. Deleting notifies all enrolled volunteers. Reopening a cancelled event is forbidden; opening an over-capacity one is refused.

### C2. Member joins the event

4. **Member** logs in, sees recommended events on the dashboard (a recommendation engine ranks events by their volunteer profile: skills, interests, languages), and/or the open event list, and clicks **Join** (`/events/{id}/join`).

5. The system checks, in order:
    - not already enrolled,
    - event is `open` (not closed/cancelled),
    - event date is still in the future,
    - not at capacity.
      If the event is **full**, the member can still join **if** they hold an un-consumed **Priority Event Registration** reward (from Flow D) — the system consumes that redemption and lets them in. Otherwise joining is refused.

6. On a successful join, the pivot row `event_volunteer` is created with `status=confirmed` and `joined_at`. Capacity is re-checked; the event **closes itself** when full.

7. The **Member** can also **Remove self** (leave) any future event, which reopens the event if it was closed due to capacity, and can see a "My Events" page with confirmed/completed/absent counts.

### C3. Admin runs the event (attendance + points)

8. **Admin** manages the volunteer list (`/events/{id}/volunteers`) and the Attendance area. For each volunteer they mark attendance_status as `confirmed`, `pending_review`, `completed`, or `absent` (with an optional absence reason). There are **bulk** actions to approve [all] pending → completed, or mark all pending → absent.

9. **The moment a volunteer is marked `completed`, the gamification engine fires** (in `GamificationService`):
    - a **base 50 points** for completing the event,
    - **+10** if they joined ≥ 7 days before the event (early bird),
    - a **streak bonus** (+25/+50/+100) if their volunteering streak crosses 3/5/10 (streak rolls over within 60 days comfortably — see Flow D for streak logic),
    - **+20** if the event was high-impact category (religious / education / emergency),
    - the member's points increase (`total_points` and `available_points`), a `point_transaction` is logged with a breakdown, and a notification is sent.
    - **Badges are checked & auto-awarded** (see Flow D), and a **tier upgrade** can fire.

10. A volunteer is only ever paid points once (the `points_awarded` flag makes it idempotent).

---

## Flow D — Gamification, Rewards & Recognition

This is the long-running lifecycle that continues across nearly every flow. It is where the **Member** accumulates, converts, and occasionally spends points — adjudicated by the **Admin**.

### D1. How the Member earns points

Points accrue through the money-handling flows already described:

- **Event completion** (Flow C) — base + bonuses,
- **Referrals** (Flow E) — 15 points per successful new signup,
- **Badge earnings** — automatic (below),
- **Admin adjustments** — see D3.

### D2. Badges, tiers & streaks

- **Badges** are catalogued with `first_step`, `consistent`, `dedicated`, `helping_hand`, `masjid_hero` and category badges (e.g. `religious_scholar`, `emergency_responder`) — each badge has a `points_awarded` reward.
- **`checkAndAwardBadges`** automatically grants whichever badge thresholds the member now passes (counting completed events: 1=First Step, 5=Consistent, 10=Dedicated, 25=Helping Hand, 50=Masjid Hero, plus 10 religious / 5 emergency category completions), and each newly awarded badge also **credits its own points**.
- **Tiers** (Bronze 0 → Silver 200 → Gold 500 → Platinum 1000 → Diamond 2000) are derived from the member's `total_points`. The member is notified when they climb to a new tier, and the UI shows their progress bar to the next tier.

### D3. Admin adjusts points (correction / reward)

- **Admin** (`/admin/gamification`) sees all members ranked by points with a search box. For any member they can:
    - add points (creates an `adjusted` transaction), or
    - deduct points (clamps at the member's available balance; creates a `revoked` transaction with an admin note for the record).
    - They also have a full per-member **transaction history** page.

### D4. The Member redeems a reward

- The **Member** browses available rewards (`/gamification/rewards`) — facilities, recognition, events, merchandise — each with a `points_cost`, filtered to `is_active` and not expired/out-of-stock, and sees their own **available points**.
- On **Redeem** (`/rewards/{reward}/redeem`), `GamificationService::redeemReward`:
    - checks the member's available points ≥ cost,
    - deducts available / adds redeemed points,
    - creates a `reward_redemption` with **status = pending**, a unique `claim_code`, and logs a `redeemed` point transaction.
- **Special rules**:
    - The **Appreciation Certificate** reward is promoted to `claimed` immediately and a **PDF certificate** is generated (`CertificateService`).
    - The **Priority Event Registration** reward is only activated when actually used to join a full event (Flow C).
    - Downloads of the certificate are only allowed to that member and only once `claimed`.

### D5. Admin fulfills the redemption

- **Admin** sees pending redemptions (`/admin/gamification/redemptions`). For each, they either:
    - **fulfill** → status becomes `claimed`, with `fulfilled_by`, `fulfilled_at`, notes; the member is notified.
    - **reject** → the member's `available_points` are **refunded** and `redeemed_points` decreased (a `refunded` transaction), status becomes `rejected`.

### D6. Leaderboard

- The Member can view **global** (all-time), **monthly**, and **category** leaderboards and their own rank — cached for 60 minutes.
- A member who ticked "hide from leaderboard" is excluded.

---

## Flow E — Referrals (the growth mechanism)

This flow involves **Admin (configuring)**, **Member A (referrer)**, and **Member B (new joiner)**.

1. **Member A** generates a referral code on their profile (`/profile/referral/generate`). `GamificationService` creates an 8-char uppercase code. **Monthly limit:** A cannot regenerate a new code more than once a month (avoids churn abuse).

2. **Member A** shares that code with friend **Member B**.

3. **Member B** registers (Flow 0). If B enters A's code in the referral field, the sign-up:
    - stamps `referred_by = A.id` onto B, and
    - awards **Member A** the referral bonus (15 points) via `processReferral`, logging a `referral` transaction and notifying A.

4. Self-referral is blocked, and only one referrer can be recorded per signup (each member can only ever be referred once).

---

## Flow F — Transparency & reporting

This flow primarily involves **Admin & Treasurer** producing the reporting, with **Members** as the read-only viewers of transparency.

1. **Admin / Treasurer** open `/reports`, choose monthly or yearly period (month/year pickers), and switch between tabs: **donations**, **events**, **attendance**, **withdrawals** and **gamification**. Each tab has its own sortable, filterable table (e.g. sort donations by date/amount/category/source; attendance by event/volunteer/status).

2. They can export any tab to **CSV** or **PDF** (routes under `/reports/export/...`).

3. **Members** can view the public **Transparency** page (`/transparency`) — this page reads real data: month & year incoming totals by category, and **only approved** withdrawals spent in the current year, listed as expenses. This is how the jamaiah sees the mosque is honest.

---

## Flow H — Administration & Configuration

A few admin-only flows complete the system.

- **Registration codes** (see Flow 0): Admin regenerates admin/treasurer code tokens from `/admin/settings`.
- **Fund purposes** (see Flow A): create/edit/deactivate fund purpose labels.
- **Amils** (`/admin/amils`): Admin appoints/removes amil flags on any user; amils appear in the zakat akad dropdown when the Admin records a zakat donation.
- **Gaming catalog** (`/admin/gamification/{badges|rewards|tiers}`): Admin **creates / edits / deletes** badges (with uploading icon), rewards (with image), and tier milestones. Rewards with redemption history cannot be hard-deleted (deactivate instead). Badge/reward toggles live/dead.

---

## Quick reference — key files

| Concern                | File(s)                                                                                                                                                 |
| ---------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Routing & roles        | `routes/web.php`, `app/Http/Middleware/CheckRole.php`, `config/roles.php`, `app/Models/User.php`                                                        |
| Auth & registration    | `app/Http/Controllers/AuthController.php`, `app/Services/RegistrationCodeService.php`                                                                   |
| Donations              | `app/Http/Controllers/DonationController.php`, `app/Models/Donation.php`, `app/Models/ZakatAkad.php`, `app/Services/ReceiptNumberService.php`           |
| Withdrawals            | `app/Http/Controllers/WithdrawalController.php`, `app/Models/WithdrawalRequest.php`                                                                     |
| Events & volunteers    | `app/Http/Controllers/EventController.php`, `app/Http/Controllers/VolunteerController.php`, `app/Services/RecommendationService.php`                    |
| Gamification           | `app/Http/Controllers/GamificationController.php`, `app/Http/Controllers/Admin/GamificationAdminController.php`, `app/Services/GamificationService.php` |
| Rewards & certificates | `app/Models/Reward.php`, `app/Models/RewardRedemption.php`, `app/Services/CertificateService.php`                                                       |
| Reports & transparency | `app/Http/Controllers/ReportController.php`, `app/Http/Controllers/VolunteerController.php@transparency`                                                |
| Admin config           | `app/Http/Controllers/Admin/AdminSettingsController.php`, `app/Http/Controllers/Admin/AmilAdminController.php`                                          |
