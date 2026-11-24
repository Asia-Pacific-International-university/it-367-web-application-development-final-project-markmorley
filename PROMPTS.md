# PROMPTS.md: AI usage log

**Student:** Mark Michael Morley
**Project:** Filipino Club Membership and Dues Management System: a Laravel 12 REST API with a Vue.js 3 frontend for managing club members, monthly ฿30 dues, payments, donations, and announcements.
**AI tools used:** Claude _(ChatGPT, Copilot, Cursor)_

## Rules (from the assignment)

- One step = one small, working change. Commit after every step.
- At least **40 commits on at least 12 different days** (most projects land between 60 and 100). Tiny commits piled up on the last night do not count.
- Add one entry below for **every** commit, newest at the bottom.
- Copy your prompt **exactly** as you typed it. Do not tidy it up afterwards.
- Steps you wrote yourself are logged too: write `no AI` as the tool.
- "What I changed by hand" is the most important field. If you changed nothing, say so and explain how you checked the code was right.
- Fill in the commit hash after you commit (`git log --oneline -1`).

---

## Entry template (copy this block for each step)

### Step N: _short title_

- **Date:**
- **Tool:**
- **Prompt:**
  ```text
  paste your exact prompt here
  ```
- **What the AI produced:** _one line, e.g. "migration, model and factory for Product"_
- **What I changed by hand and why:**
- **How I checked it works:** _e.g. ran `php artisan test`, tried it in Postman_
- **Commit:** `abc1234`

---

## Example entry

### Step 3: Product model and migration

- **Date:** 2026-11-12
- **Tool:** ChatGPT
- **Prompt:**
  ```text
  In my Laravel 12 API, create a migration, model and factory for Product with
  name (string), sku (unique string), price (decimal 8,2), stock (integer, default 0)
  and a category_id foreign key to categories. A Product belongs to a Category.
  ```
- **What the AI produced:** migration, `Product` model with `belongsTo(Category)`, factory.
- **What I changed by hand and why:** the AI used `float` for price; I changed it to `decimal(8, 2)` because floats round money badly. Added `cascadeOnDelete()` on `category_id`. Added `price` to `$casts` as `decimal:2`.
- **How I checked it works:** `php artisan migrate:fresh --seed`, then checked the table in TablePlus.
- **Commit:** `4f9c2d1`

---

## My log

<!-- Start your entries here. -->

### Step 1: Initialize the Laravel project

- **Date:** 2026-11-12
- **Tool:** Not recorded
- **Prompt:**
  ```text
  No prompt is recorded for this commit.
  ```
- **What the AI produced:** Not recorded. The commit contains the initial Laravel application, including API authentication and Notes examples, tests, configuration, and project documentation.
- **What I changed by hand and why:** The commit history does not record which files were authored or edited by hand.
- **How I checked it works:** Not recorded in the commit history.
- **Commit:** `730bedb`

---

### Step 2: Add GitHub Classroom autograding

- **Date:** 2026-11-12
- **Tool:** GitHub Classroom setup; AI use not recorded
- **Prompt:**
  ```text
  No prompt is recorded for this automated Classroom setup commit.
  ```
- **What the AI produced:** No AI output is recorded. The commit adds `.classroom50.yaml` and the GitHub Actions autograde workflow.
- **What I changed by hand and why:** Not recorded.
- **How I checked it works:** Not recorded in the commit history.
- **Commit:** `ce9f040`

---

### Step 3: Add the problem statement and primary users

- **Date:** 2026-11-15
- **Tool:** Copilot
- **Prompt:**
  ```text
  ### 1. Problem and Users
  The Filipino Club currently manages its membership information and dues payments using spreadsheets and manual records. This approach can make it difficult for club officers and the treasurer to keep track of which members have paid and which members still have outstanding dues. Payment records can also become scattered or difficult to find, making it time-consuming to prepare financial reports. In addition, members may have difficulty checking their own payment history. The Club Membership and Dues Management System will provide a centralized platform where club officers, the treasurer, and members can manage and access relevant membership and payment information more easily.

  **Primary users:**

  - **Club officers:** Manage club membership information and oversee membership and dues activities.
  - **Treasurer:** Record and monitor dues payments, check unpaid balances, and prepare financial reports.
  - **Members:** View their membership information and check their own payment history.

  Put this in ## 1. Problem and User
  ```
- **What the AI produced:** Added the requested problem description and primary users to the PRD.
- **What I changed by hand and why:** The problem statement and user descriptions came from my prompt. No separate manual edits are recorded for this commit.
- **How I checked it works:** Reviewed the updated PRD section and the resulting diff; no tests were needed for this documentation change.
- **Commit:** `d96d6ee`

---

### Step 4: Define roles, stories, business rules, and data model

- **Date:** 2026-11-15
- **Tool:** Copilot
- **Prompt:**
  ```text
  ## 5. Data Model
  The system will use the following models to store and manage membership, dues, payments, donations, and announcements. The existing Laravel User model will handle user authentication and login accounts.

  ### 5.1 Proposed Models
  ModelPurposeExample FieldsMemberProfileStores personal information about club members.user_id, full_name, student_id, phoneMonthlyDuesTracks each member's monthly dues requirement and payment status.member_profile_id, month, year, required_amountPaymentStores individual dues payment transactions.monthly_dues_id, amount, payment_date, payment_methodDonationStores the donation portion of a payment separately.payment_id, amountAnnouncementStores announcements published by club officers.user_id, title, message, published_at
  ### 5.2 Relationships

  - A User has one MemberProfile.
  - A MemberProfile can have many MonthlyDues records.
  - A MonthlyDues record can have many Payment records.
  - A Payment can have one Donation record.
  - A User (Club Officer / Admin) can publish many Announcements.

  ### 5.3 Monthly Dues and Donations
  Each member must contribute at least ฿30 per month. The system will calculate the total contributions for each month.

  - If the monthly total is at least ฿30, the member is marked as paid.
  - If the monthly total is below ฿30, the member is marked as unpaid.
  - Any amount above ฿30 is recorded as a donation.
  - Extra contributions do not carry over to the following month.

  ### 5.4 Announcement Management
  Only users with the Club Officer / Admin role can create and publish announcements. Treasurers and members can view published announcements but cannot create or edit them.

  Add this to no.5
  ```
- **What the AI produced:** Updated the PRD with user roles and permissions, ten user stories and acceptance criteria, business rules, five proposed models, relationships, monthly dues/donation rules, and announcement management. This commit combines several separate prompts; the code block shows the final request before the commit.
- **What I changed by hand and why:** The requirements and model details were supplied in my requests. Any additional manual edits are not identified in the commit history.
- **How I checked it works:** Reviewed the resulting PRD and commit diff; no tests were needed for this documentation change.
- **Commit:** `e25e833`

---

### Step 5: Add the initial ERD

- **Date:** 2026-11-15
- **Tool:** Not recorded
- **Prompt:**
  ```text
  No prompt is recorded for this diagram commit.
  ```
- **What the AI produced:** Not recorded. The commit adds `docs/erd.png`.
- **What I changed by hand and why:** Not recorded.
- **How I checked it works:** Not recorded in the commit history.
- **Commit:** `7a84a3f`

---

### Step 6: Revise the ERD

- **Date:** 2026-11-15
- **Tool:** ChatGPT (diagram generated with Python)
- **Prompt:**

  ```
  Revise the Filipino Club Membership and Dues Management System ERD to reflect the project requirements and relationships.
  ```
- **What the AI produced:** ChatGPT created a revised ERD diagram for the project. The diagram included users, member profiles, monthly dues, payments, donations, and announcements.
- **What I changed by hand and why:** Not recorded.
- **How I checked it works:** Not recorded. I should verify that the diagram opens correctly and that its entities and relationships match the approved PRD.
- **Commit:** `e0c8905`

---

### Step 7: Add the initial architecture diagram

- **Date:** 2026-11-15
- **Tool:** ChatGPT and Python
- **Prompt:**

  ```
  check whether the architecture diagram for my Filipino Club Membership and Dues Management System, showing the frontend, backend API, database, and hosting plan fits my prd.
  ```
- **What the AI produced:** ChatGPT helped check the initial architecture diagram based on the project requirements I provided.
- **What I changed by hand and why:** I reviewed the diagram against my PRD and identified the technologies and hosting details that needed to be represented. Any specific manual edits should be recorded here.
- **How I checked it works:** I checked that the diagram file opened and reviewed whether its components matched the architecture described in my PRD.
- **Commit:** `a210d62`

---

### Step 8: Revise the architecture diagram

- **Date:** 2026-11-15
- **Tool:** ChatGPT and Python
- **Prompt:**

  ```
  Update my architecture diagram to show Vue.js 3 with Vite as the frontend and Laravel 12 as the REST API backend.
  ```
- **What the AI produced:** ChatGPT helped revise the architecture diagram to reflect my decision to use Vue.js 3 with Vite for the frontend and Laravel 12 for the backend API.
- **What I changed by hand and why:** I chose Vue.js 3 with Vite for the frontend and requested that the diagram reflect this decision. Record any additional edits you made directly to the diagram here.
- **How I checked it works:** I reviewed the updated diagram to confirm that it showed Vue.js 3 with Vite and Laravel 12, and that it matched my PRD.
- **Commit:** `5dcd6e0`

---

### Step 9: Add API, frontend, and project scope plans

- **Date:** 2026-11-15
- **Tool:** Copilot
- **Prompt:**
  ```text
  add this number 10:
  ## 10. Changelog
  DateChangesNovember 10, 2026Created the initial PRD and identified the problem, current management process, and primary users.November 11, 2026Defined user roles, permissions, and the main user stories for the system.November 12, 2026Added business rules for monthly dues, payment methods, and donations.November 13, 2026Designed the initial data model and created the ERD and architecture diagrams.November 14, 2026Selected Vue.js 3 with Vite for the frontend and updated the frontend architecture.November 15, 2026Added the API plan, level-up features, and out-of-scope requirements.
  ```
- **What the AI produced:** Added the Laravel 12/Sanctum API plan and endpoint tables, Vue 3/Vite frontend plan, level-up features, out-of-scope requirements, and changelog. The commit also includes the architecture diagram reference. This commit combines several separate prompts; the code block shows the final request before the commit.
- **What I changed by hand and why:** The requirements and wording came from my prompts. Any additional manual edits are not identified in the commit history.
- **How I checked it works:** Reviewed the updated PRD and commit diff; no tests were needed for this documentation change.
- **Commit:** `5652c57`

---

### Step 10: Update the ERD diagram

- **Date:** 2026-11-24
- **Tool:** Copilot (the commit includes a Copilot co-author trailer)
- **Prompt:**
  ```text
  The exact prompt for this diagram update is not recorded in the available history.
  ```
- **What the AI produced:** The commit updates `docs/erd.png`; the specific AI contribution is not recorded.
- **What I changed by hand and why:** Not recorded.
- **How I checked it works:** Not recorded in the commit history.
- **Commit:** `f7dc7b2`

---

### Step 11: Record diagram prompts and revisions

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  commit and push this while also recording this in this file
  ```
- **What the AI produced:** Updated the log entries for the ERD and architecture diagram revisions with the supplied tool, prompts, output descriptions, and verification notes.
- **What I changed by hand and why:** I supplied the historical prompt and diagram details to make the log more complete.
- **How I checked it works:** Reviewed the updated entries and ran `git diff --check`.
- **Commit:** `b7451c7` (follow-up hash record: `de5516a`)

---

### Step 12: Correct the architecture diagram log entries

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ````text
  ### Step 7: Add the initial architecture diagram

  - **Date:** 2026-11-15
  - **Tool:** ChatGPT and Python
  - **Prompt:**

  ```
  check whether the architecture diagram for my Filipino Club Membership and Dues Management System, showing the frontend, backend API, database, and hosting plan fits my prd.
  ```
  - **What the AI produced:** ChatGPT helped check the initial architecture diagram based on the project requirements I provided.
  - **What I changed by hand and why:** I reviewed the diagram against my PRD and identified the technologies and hosting details that needed to be represented. Any specific manual edits should be recorded here.
  - **How I checked it works:** I checked that the diagram file opened and reviewed whether its components matched the architecture described in my PRD.
  - **Commit:** `a210d62`

  ---

  ### Step 8: Revise the architecture diagram

  - **Date:** 2026-11-15
  - **Tool:** ChatGPT and Python
  - **Prompt:**

  ```
  Update my architecture diagram to show Vue.js 3 with Vite as the frontend and Laravel 12 as the REST API backend.
  ```
  - **What the AI produced:** ChatGPT helped revise the architecture diagram to reflect my decision to use Vue.js 3 with Vite for the frontend and Laravel 12 for the backend API.
  - **What I changed by hand and why:** I chose Vue.js 3 with Vite for the frontend and requested that the diagram reflect this decision. Record any additional edits you made directly to the diagram here.
  - **How I checked it works:** I reviewed the updated diagram to confirm that it showed Vue.js 3 with Vite and Laravel 12, and that it matched my PRD.
  - **Commit:** `5dcd6e0`

  ---

  change my step 7 and 8
  ````
- **What the AI produced:** Revised the Step 7 and Step 8 entries with the supplied prompts, tool details, descriptions, and verification notes.
- **What I changed by hand and why:** I supplied the corrected prompt text and descriptions for both diagram steps.
- **How I checked it works:** Reviewed the updated entries and ran `git diff --check`.
- **Commit:** `4822f3b`

---

### Step 13: Revise data model and event registration requirements

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  add this to number five since I revised this:
  ### 5.1 Proposed Models

  Model | Purpose | Example fields

  MemberProfile | Stores personal information about club members. | user_id, full_name, student_id, phone

  MonthlyDues | Tracks each member's monthly dues requirement and payment status. | member_profile_id, month, year, required_amount

  Payment | Stores individual dues payment transactions and identifies who recorded them. | monthly_dues_id, recorded_by_user_id, amount, dues_portion, payment_date, payment_method

  Donation | Stores the donation portion of a payment separately. | payment_id, amount

  Announcement | Stores announcements published by club officers. | author_user_id, title, message, published_at

  ClubEvent | Stores club events created by club officers. | created_by_user_id, title, description, event_date, location

  EventRegistration | Records a member's registration for a club event, including registration details. | member_profile_id, club_event_id, registered_at, status
  The existing `User` model will store login information and each user's role.

  ### 5.2 Relationships

  - A User has one MemberProfile.
  - A MemberProfile can have many MonthlyDues records.
  - A MonthlyDues record can have many Payment records.
  - A User can record many Payment records as the Treasurer.
  - A Payment can have zero or one Donation record.
  - A User with the Club Officer / Admin role can publish many Announcements.
  - A User with the Club Officer / Admin role can create many ClubEvents.
  - A MemberProfile can register for many ClubEvents, and a ClubEvent can have many registered MemberProfiles. This many-to-many relationship is handled through the EventRegistration model.
  - Each EventRegistration belongs to one MemberProfile and one ClubEvent.
  The EventRegistration model is the pivot model for the many-to-many relationship. It stores additional information about the relationship, including the registration date and registration status.

  ### 5.3 Monthly Dues and Donations
  Each member must contribute at least ฿30 per month. The system will calculate the total contributions for each member for that month.

  - If the monthly amount applied to dues reaches at least ฿30, the member is marked as paid.
  - If the monthly amount applied to dues is below ฿30, the member is marked as unpaid.
  - If a member contributes more than the remaining amount needed to reach ฿30 in a month, the excess is recorded as a donation.
  - Extra contributions do not carry over to the following month.
  - Financial reports will show dues and donations separately.
  The system will calculate the dues portion across all payments for that member and month. For example, if a member pays ฿20 and later pays another ฿20 in the same month, ฿30 is applied to dues and the remaining ฿10 is recorded as a donation.

  ### 5.4 Announcement Management
  Only users with the Club Officer / Admin role can create, edit, or delete announcements. Treasurers and members can view published announcements but cannot create, edit, or delete them.

  ### 5.5 Club Event Registration
  Club officers can create club events. Members can register for events and view their own registrations. Each registration will store the registration date and status.

  The system will prevent a member from registering for the same event more than once. Event registration records will be stored in the EventRegistration model, which connects MemberProfile and ClubEvent and includes the additional pivot fields `registered_at` and `status`.

  and record this commit and push in the prompts.md file
  ```
- **What the AI produced:** Revised PRD section 5 with the expanded payment and donation fields, ClubEvent and EventRegistration models, relationships, monthly dues allocation example, announcement permissions, and event registration rules.
- **What I changed by hand and why:** I supplied the revised models, fields, relationships, and business requirements.
- **How I checked it works:** Reviewed the updated PRD section and ran `git diff --check`; no tests were needed for this documentation change.
- **Commit:** To be filled after committing.
