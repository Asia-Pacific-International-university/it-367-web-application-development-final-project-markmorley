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

### Example: Product model and migration

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
- **Commit:** `8bf11ed`

---

### Step 14: Create the user role migration scaffold

- **Date:** 2026-11-24
- **Tool:** Not recorded
- **Prompt:**
  ```text
  No prompt is available in the recorded history for this commit.
  ```
- **What the AI produced:** AI involvement is not recorded. The commit adds the Laravel migration scaffold `2026_11_24_044317_add_role_to_users_table.php`; its `up` and `down` schema callbacks are empty in the committed version.
- **What I changed by hand and why:** Not recorded for this commit.
- **How I checked it works:** Not recorded in the commit history.
- **Commit:** `f192f24`

---

### Step 15: Implement the user role migration

- **Date:** 2026-11-24
- **Tool:** Copilot and Claude
- **Prompt:**
  ````text
  Add this to database/migrations/2026_11_24_044317_add_role_to_users_table.php:
  public function up(): void
  {
      Schema::table('users', function (Blueprint $table) {
          $table->string('role')->default('member')->index();
      });
  }

  public function down(): void
  {
      Schema::table('users', function (Blueprint $table) {
          $table->dropColumn('role');
      });
  }
  ````
- **What the AI produced:** Updated the migration to add an indexed `role` string column defaulting to `member` and to drop the column on rollback.
- **What I changed by hand and why:** it's worth recording that the first migration run had an empty up(), that you found it with migrate:status and db:table users, and that you fixed it and ran migrate:fresh.
- **How I checked it works:** Ran `php -l` and `git diff --check` on the migration; both passed.
- **Commit:** `70b2b43`

---

### Step 16: Add user role constants and helper

- **Date:** 2026-11-24
- **Tool:** Copilot and Claude
- **Prompt:**
  ````text
  Add the constants at the top of the class, just under the `use` line for the traits:

  php

  ```
  public const ROLE_ADMIN = 'admin';
  public const ROLE_TREASURER = 'treasurer';
  public const ROLE_MEMBER = 'member';
  ```

  Add the helper below the `notes()` method:

  php

  ```
  public function hasRole(string ...$roles): bool
  {
      return in_array($this->role, $roles, true);
  }
  ```
  ````
- **What the AI produced:** Added role constants for admin, treasurer, and member, plus a strict `hasRole()` helper to `User`.
- **What I changed by hand and why:** I supplied the requested constants and helper implementation.
- **How I checked it works:** Ran `php -l app/Models/User.php`; no syntax errors were reported.
- **Commit:** `d1e7f13`

---

### Step 17: Create the member profiles migration

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ````text
  Add this
  public function up(): void
  {
      Schema::create('member_profiles', function (Blueprint $table) {
          $table->id();
          $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
          $table->string('full_name');
          $table->string('student_id')->unique();
          $table->string('phone')->nullable();
          $table->softDeletes();
          $table->timestamps();
      });
  }

  public function down(): void
  {
      Schema::dropIfExists('member_profiles');
  }
  ````
- **What the AI produced:** Added a migration that creates `member_profiles` with a unique, cascading `user_id`, unique `student_id`, contact fields, soft deletes, timestamps, and a rollback that drops the table.
- **What I changed by hand and why:** I provided the requested schema and migration methods, I found the empty up() and fixed it with migrate:rollback..
- **How I checked it works:** `php -l` passed. `migrate:status` showed the migration as ran, but `db:table member_profiles` showed only `id`, `created_at`, and `updated_at`; the database did not yet reflect the current migration definition.
- **Commit:** `5f9fd31`

---

### Step 18: Add the MemberProfile model

- **Date:** 2026-11-24
- **Tool:** Not recorded
- **Prompt:**
  ```text
  The original prompt for creating this model is not available in the recorded history. Current request: record this to prompts.md
  ```
- **What the AI produced:** AI involvement is not recorded. The commit adds the `MemberProfile` Eloquent model with fillable profile fields, soft deletes, and a `belongsTo` relationship to `User`.
- **What I changed by hand and why:** Ran `php artisan make:model MemberProfile` without `-m`, because the `member_profiles` migration already existed. Replaced the generated class with a model that uses `SoftDeletes`, to match the `deleted_at` column from `softDeletes()`, and `HasFactory`, which I need for the factory in the next step. Set `$fillable` to `user_id`, `full_name`, `student_id` and `phone`. Added a `user()` `belongsTo` relationship. I did not make `role` or anything permission-related fillable on this model. I earlier kept `role` out of `User::$fillable`, so nobody can give themselves a role through mass assignment.
- **How I checked it works:** Ran the Tinker, created a MemberProfile for the first user and confirmed $p->user->name returned the user's name. Ran php artisan test: 16 passed.
- **Commit:** `1dd21e3`

---

### Step 19: Add memberProfile relationship to User

- **Date:** 2026-11-24
- **Tool:** Claude
- **Prompt:**
  ```text
  Original prompt not available in the recorded history. The commit message was: Add memberProfile relationship to User model.
  ```
- **What the AI produced:** The `memberProfile()` `hasOne` relationship and the `HasOne` import for `User`.
- **What I changed by hand and why:** I added the import and the method to `User.php`, below `notes()` and above `hasRole()`, and saved the file. I chose to keep this relationship next to the other relationships and not touch `$fillable`. I understand that `Model::preventLazyLoading` is on in `AppServiceProvider`, so any controller that lists users needs `->with('memberProfile')`.
- **How I checked it works:** In Tinker I ran `App\Models\User::first()->memberProfile->full_name` and got "Test Member". I ran `php artisan test` and all 16 tests passed.
- **Commit:** `9a511a7`

---

### Step 20: Add the MemberProfile factory

- **Date:** 2026-11-24
- **Tool:** Claude and Copilot
- **Prompt:**
  ````text
  Replace the `definition()` method with the code below, then save:

  php

  ```
  public function definition(): array
  {
      return [
          'user_id' => User::factory(),
          'full_name' => fake()->name(),
          'student_id' => fake()->unique()->numerify('S#####'),
          'phone' => fake()->optional()->numerify('08########'),
      ];
  }
  ```
  ````
- **What the AI produced:** `MemberProfileFactory` with `user_id` from `User::factory()`, a random `full_name`, a unique `student_id` like `S52443`, and an optional phone number.
- **What I changed by hand and why:** Generated the file with `php artisan make:factory MemberProfileFactory --model=MemberProfile`, added the `User` import, replaced `definition()`, and saved. I kept `unique()` on `student_id` because that column is unique in the database, and `optional()` on `phone` because it is nullable.
- **How I checked it works:** In Tinker, ran `App\Models\MemberProfile::factory()->create()`. It created a profile with a linked user, and `$p->user->name` and `$p->student_id` returned values. `phone` was `null` on that run, as expected because of `optional()`. Ran `php artisan test`: 16 passed.
- **Commit:** `24396d0`

---

### Step 21: Create the monthly dues migration

- **Date:** 2026-11-24
- **Tool:** Not recorded
- **Prompt:**
  ```text
  Original prompt not available in the recorded history. Current request: record this to prompt.md.
  ```
- **What the AI produced:** AI involvement is not recorded. The commit adds a `monthly_dues` migration with a member-profile foreign key, month and year, a default required amount of 30.00, timestamps, and a unique member/month/year constraint.
- **What I changed by hand and why:** Generated the file with `make:migration`, pasted the code into `up()`, and saved it before running `migrate` because a previous migration had run with an empty `up()`.
- **How I checked it works:** Ran `php artisan migrate`, then `php artisan db:table monthly_dues` and confirmed the 7 columns, unique index, and foreign key.
- **Commit:** `e195cd1`

---

### Step 22: Add the MonthlyDues model

- **Date:** 2026-11-24
- **Tool:** Claude
- **Prompt:**
  ```text
    Open app/Models/MonthlyDues.php, replace the contents with this, and save:

  php
  <?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;

  class MonthlyDues extends Model
  {
      use HasFactory;

      protected $table = 'monthly_dues';

      protected $fillable = [
          'member_profile_id',
          'month',
          'year',
          'required_amount',
      ];

      protected function casts(): array
      {
          return [
              'month' => 'integer',
              'year' => 'integer',
              'required_amount' => 'decimal:2',
          ];
      }

      public function memberProfile(): BelongsTo
      {
          return $this->belongsTo(MemberProfile::class);
      }
  }
  ```
- **What the AI produced:** The `MonthlyDues` Eloquent model with its table name, fillable fields, casts, and `memberProfile()` `belongsTo` relationship.
- **What I changed by hand and why:** Replaced the contents of `app/Models/MonthlyDues.php` with the supplied model implementation and saved it.
- **How I checked it works:** Ran `php -l app/Models/MonthlyDues.php`; no syntax errors were reported.
- **Commit:** `e5c743b`

---

### Step 23: Add the monthlyDues relationship to MemberProfile

- **Date:** 2026-11-24
- **Tool:** Not recorded
- **Prompt:**
  ```text
  Original prompt for adding this relationship is not available in the recorded history.
  ```
- **What the AI produced:** AI involvement is not recorded. The commit adds the `HasMany` import and a `monthlyDues()` relationship returning `$this->hasMany(MonthlyDues::class)`.
- **What I changed by hand and why:** Not recorded.
- **How I checked it works:** Ran `php -l app/Models/MemberProfile.php`; no syntax errors were reported.
- **Commit:** `84c975a`

---

### Step 24: Add the payments migration

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ````text
  Open the new file, replace `up()` with the code below, and **save before you run the migration**:

  php

  ```
  public function up(): void
  {
      Schema::create('payments', function (Blueprint $table) {
          $table->id();
          $table->foreignId('member_profile_id')->constrained()->restrictOnDelete();
          $table->foreignId('recorded_by_user_id')->constrained('users')->restrictOnDelete();
          $table->decimal('amount', 8, 2);
          $table->date('payment_date');
          $table->string('payment_method');
          $table->timestamps();

          $table->index('payment_date');
      });
  }
  ```
  ````
- **What the AI produced:** Updated the migration to create `payments` with member and recorder foreign keys, amount, payment date, payment method, timestamps, and a payment-date index.
- **What I changed by hand and why:** I supplied the migration schema and asked for it to be saved before running the migration.
- **How I checked it works:** Ran `php -l database/migrations/2026_11_24_074647_create_payments_table.php`; no syntax errors were reported. I did not run the migration as requested.
- **Commit:** `d12c370`

---

### Step 25: Add the Payment model

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ````text
  Add this
  <?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;

  class Payment extends Model
  {
      use HasFactory;

      protected $fillable = [
          'member_profile_id',
          'recorded_by_user_id',
          'amount',
          'payment_date',
          'payment_method',
      ];

      protected function casts(): array
      {
          return [
              'amount' => 'decimal:2',
              'payment_date' => 'date',
          ];
      }

      public function memberProfile(): BelongsTo
      {
          return $this->belongsTo(MemberProfile::class);
      }

      public function recordedBy(): BelongsTo
      {
          return $this->belongsTo(User::class, 'recorded_by_user_id');
      }
  }
  ````
- **What the AI produced:** Added the `Payment` Eloquent model with fillable payment fields, amount/date casts, and `memberProfile()` and `recordedBy()` relationships.
- **What I changed by hand and why:** I supplied the requested model implementation.
- **How I checked it works:** Ran `php -l app/Models/Payment.php`; no syntax errors were reported.
- **Commit:** `1436a9b`

---

### Step 26: Add the payments relationship to MemberProfile

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ````text
  Edit `app/Models/MemberProfile.php`

  The `HasMany` import is already there from step 10, so you only need to add this method below `monthlyDues()`, then **save**:

  php

  ```
  public function payments(): HasMany
  {
      return $this->hasMany(Payment::class);
  }
  ```
  ````
- **What the AI produced:** Added a `payments()` `HasMany` relationship returning `$this->hasMany(Payment::class)`.
- **What I changed by hand and why:** I requested the relationship method beneath `monthlyDues()`; the `HasMany` import was already present.
- **How I checked it works:** Ran `php -l app/Models/MemberProfile.php`; no syntax errors were reported.
- **Commit:** `8627016`

---

### Step 27: Add the Payment factory

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:** The request had two parts:
  ````text
  Open `database/factories/PaymentFactory.php`, add these imports at the top if they're missing, replace `definition()`, and **save**:

  php

  ```
  use App\Models\MemberProfile;
  use App\Models\User;
  ```
  ````
  The factory definition supplied after clarification:
  ```text
  public function definition(): array
  {
      return [
          'member_profile_id' => MemberProfile::factory(),
          'recorded_by_user_id' => User::factory()->treasurer(),
          'amount' => fake()->randomElement([30, 30, 30, 50, 100]),
          'payment_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
          'payment_method' => fake()->randomElement(['cash', 'bank_transfer']),
      ];
  }
  ```
- **What the AI produced:** Added `PaymentFactory` with generated member and treasurer relationships, payment amounts, dates, and methods.
- **What I changed by hand and why:** I supplied the factory definition. The required `UserFactory::treasurer()` state was added separately in Step 28.
- **How I checked it works:** PHP syntax checks passed for both factories and all 16 tests passed. A Tinker smoke check showed the treasurer role and generated payment attributes; resolving the related factories also created related user/profile records, while the payment itself was not saved.
- **Commit:** `7521e3b`

---

### Step 28: Add the treasurer state to UserFactory

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  No separate prompt. This supporting state was needed because the PaymentFactory definition calls User::factory()->treasurer().
  ```
- **What the AI produced:** Added `UserFactory::treasurer()` to set the user's role to `User::ROLE_TREASURER`.
- **What I changed by hand and why:** Added the state helper so `PaymentFactory` can generate a treasurer as the payment recorder.
- **How I checked it works:** `php -l` passed for `UserFactory.php`; a Tinker smoke check returned the `treasurer` role, and all 16 tests passed.
- **Commit:** `47bbd7d`

---

### Step 29: Add the payment-monthly-dues pivot migration

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ````text
  Open the new file, replace `up()` with the code below, and **save before you run the migration**:

  php

  ```
  public function up(): void
  {
      Schema::create('payment_monthly_dues', function (Blueprint $table) {
          $table->id();
          $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
          $table->foreignId('monthly_dues_id')->constrained('monthly_dues')->restrictOnDelete();
          $table->decimal('allocated_amount', 8, 2);
          $table->timestamps();

          $table->unique(['payment_id', 'monthly_dues_id']);
      });
  }
  ```
  ````
- **What the AI produced:** Updated the migration to create `payment_monthly_dues` with payment and monthly-dues foreign keys, an `allocated_amount`, timestamps, and a unique constraint across both foreign keys.
- **What I changed by hand and why:** I supplied the migration schema and requested that it be saved before running the migration.
- **How I checked it works:** Ran `php -l database/migrations/2026_11_24_080501_create_payment_monthly_dues_table.php`; no syntax errors were reported. I did not run the migration as requested.
- **Commit:** `09120f1`

---

### Step 30: Add the monthly dues relationship to Payment

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add a Payment::monthlyDues() many-to-many relationship using the payment_monthly_dues pivot, its explicit foreign keys, allocated_amount, and timestamps. Verify it in Tinker, run the tests, commit the model change, and log the work here. Explain what withPivot does.
  ```
- **What the AI produced:** Added `Payment::monthlyDues()` using the explicit pivot table and key names, with `allocated_amount` and pivot timestamps.
- **What I changed by hand and why:** I specified the relationship implementation and validation steps. `withPivot('allocated_amount')` tells Eloquent to include that extra pivot column on each related dues model, so callers can read it as `$due->pivot->allocated_amount`; without it, the custom pivot value is not available on the loaded pivot model. `withTimestamps()` keeps the pivot's timestamp columns updated.
- **How I checked it works:** The Tinker check linked the first payment and dues record if not already linked and returned `30` for the pivot allocation. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `d30a884`

---

### Step 31: Add the payments relationship to MonthlyDues

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add MonthlyDues::payments() as a many-to-many relationship using payment_monthly_dues, with monthly_dues_id as this model's pivot key and payment_id as the related model's pivot key. Include allocated_amount and pivot timestamps. Verify the allocation from both relationship directions, run the tests, commit the model change, and log the commit hash.
  ```
- **What the AI produced:** Added `MonthlyDues::payments()` with the pivot keys in the reverse order from `Payment::monthlyDues()`, and included the allocation column and pivot timestamps.
- **What I changed by hand and why:** I specified the relationship's key order and validation steps. The first pivot key, `monthly_dues_id`, references the current `MonthlyDues` model; the second, `payment_id`, references `Payment`.
- **How I checked it works:** Tinker showed one linked record and the same `30` allocation when reading from a payment to its dues and from the dues to its payments. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `fca3701`

---

### Step 32: Add the donations migration

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Create the donations table with a unique payment_id foreign key that cascades on delete, an amount decimal(8, 2), and timestamps. Run the migration and inspect the table with db:table. Commit the migration and log it in PROMPTS.md.
  ```
- **What the AI produced:** Added a migration that creates `donations` with an id, unique payment foreign key, exact decimal amount, and timestamps.
- **What I changed by hand and why:** I specified the schema. `payment_id` is unique so one payment can have at most one donation. The donation cascades with its payment because it represents that payment's excess and has no independent meaning; in contrast, a payment's member-profile foreign key restricts deleting a profile that still has payment records. I left `DECISIONS.md` unchanged at the user's direction.
- **How I checked it works:** `php -l` reported no syntax errors, `php artisan migrate` completed, and `php artisan db:table donations` showed five columns (`id`, `payment_id`, `amount`, `created_at`, `updated_at`), a unique index on `payment_id`, and a foreign key to `payments` with cascade on delete.
- **Commit:** `aa6829a`

---

### Step 33: Add the Donation model

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Create app/Models/Donation.php without generating another migration. Add fillable payment_id and amount fields, cast amount to decimal:2, and define the belongs-to Payment relationship. Verify the cast and relationship in Tinker, run the tests, and commit the model.
  ```
- **What the AI produced:** Added the `Donation` model with `HasFactory`, the requested fillable fields, a two-decimal amount cast, and a `BelongsTo` payment relationship.
- **What I changed by hand and why:** I specified the model structure and validation steps. The amount is represented as an exact decimal, and the payment relationship resolves the donation's owning payment.
- **How I checked it works:** Tinker returned `"10.00"` for the donation amount and `"30.00"` for the related payment amount. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `7222021`

---

### Step 34: Add the donation relationship to Payment

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add Payment::donation() as a HasOne relationship to Donation. Verify a payment with a donation returns it and a payment without a donation returns null, run the tests, commit the model change, and log the commit hash.
  ```
- **What the AI produced:** Added the `HasOne` import and `Payment::donation()` relationship.
- **What I changed by hand and why:** I specified `hasOne` because the unique `donations.payment_id` column allows at most one donation for each payment.
- **How I checked it works:** Tinker returned `"10.00"` for a payment with a donation and `null` for a payment without one. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `d44f6bf`

---

### Step 35: Add the Donation factory

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Create DonationFactory for Donation with a Payment factory for payment_id and a random amount from [5, 10, 20, 50]. Verify the generated donation and its payment's member profile in Tinker, run the tests, commit the factory, and log the hash.
  ```
- **What the AI produced:** Added `DonationFactory`, which creates a related payment and selects a sample donation amount.
- **What I changed by hand and why:** I specified the related `Payment` factory and realistic test/demo amounts. These sample amounts are not guaranteed to match a payment's excess; the future PaymentService and seeder will be responsible for that business calculation.
- **How I checked it works:** Tinker created a donation with amount `"10.00"` and returned member profile ID `5` through its related payment. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `43d4137`

---

### Step 36: Add the announcements migration

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Create an announcements table with an author_user_id foreign key to users that restricts deletion, a title, text message, nullable indexed published_at, and timestamps. Run the migration, inspect it with db:table, commit the migration, and log the hash.
  ```
- **What the AI produced:** Added the announcements migration with author, content, publication timestamp, and created/updated timestamp columns.
- **What I changed by hand and why:** I specified the schema. `author_user_id` explicitly references `users`; deletion is restricted to preserve announcements if an author account is removed. `message` uses text for longer content, and nullable `published_at` supports drafts.
- **How I checked it works:** `php -l` passed, `php artisan migrate` completed, and `php artisan db:table announcements` showed 7 columns (`id`, `author_user_id`, `title`, `message`, `published_at`, `created_at`, `updated_at`), an index on `published_at`, and a `users` foreign key with restrict on delete.
- **Commit:** `a04ed3d`

---

### Step 37: Add the Announcement model

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Create app/Models/Announcement.php without another migration. Add fillable author_user_id, title, message, and published_at fields, cast published_at to datetime, and define the author belongs-to relationship using author_user_id. Verify it in Tinker, run the tests, commit the model, and log it.
  ```
- **What the AI produced:** Added the `Announcement` model with its fillable fields, `published_at` datetime cast, and author relationship.
- **What I changed by hand and why:** I specified the model structure. The relationship uses `author_user_id` because the default key inferred from `author()` would be `author_id`. Although fillable, the author ID must be set from the authenticated user by the controller, not accepted from request data.
- **How I checked it works:** Tinker created an announcement and returned its author's name and publication date `2026-11-24`. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `62474bb`

---

### Step 38: Add the announcements relationship to User

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add User::announcements() as a HasMany relationship using author_user_id. Verify the first user's announcement count and title in Tinker, run the tests, commit the model change, and log the hash.
  ```
- **What the AI produced:** Added `User::announcements()` using the explicit `author_user_id` foreign key.
- **What I changed by hand and why:** I specified the relationship and key. The explicit key is needed because Laravel would otherwise infer `user_id` from the related table.
- **How I checked it works:** Tinker returned count `1` and title `"Welcome"` for the first user. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `90cbdb7`

---

### Step 39: Add the admin state to UserFactory

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  The AnnouncementFactory needs User::factory()->admin(), but UserFactory currently has no admin state. Add and commit the missing state before creating AnnouncementFactory.
  ```
- **What the AI produced:** Added `UserFactory::admin()` to set the user's role to `User::ROLE_ADMIN`.
- **What I changed by hand and why:** I added the missing prerequisite state because the announcement factory requires admin authors.
- **How I checked it works:** `php -l database/factories/UserFactory.php` passed, and Tinker returned `"admin"` for `User::factory()->admin()->make()->role`.
- **Commit:** `c45e34c`

---

### Step 40: Add the Announcement factory

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Create AnnouncementFactory with an admin author, a four-word fake title, a fake paragraph message, and a published_at date within the previous month. Add a draft() state that sets published_at to null. Verify both states in Tinker, run the tests, commit the factory, and log the hash.
  ```
- **What the AI produced:** Added `AnnouncementFactory` with generated admin authors, sample announcement content, recent publication dates, and a draft state.
- **What I changed by hand and why:** I specified the factory attributes and the `draft()` state for creating unpublished announcements.
- **How I checked it works:** Tinker returned `"admin"` and a Carbon publication date for a published announcement, and `null` for a draft. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `1dbc7c2`

---

### Step 41: Seed demo users for each role

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add fixed local demo users for admin, treasurer, and member roles, and create a member profile for the demo member with student ID S10001. Preserve the existing Notes seeding. Rebuild and seed the local database, verify the roles and profile in Tinker, run the tests, commit the seeder, and log the hash.
  ```
- **What the AI produced:** Updated `DatabaseSeeder` to seed the requested role accounts and member profile before the existing demo user and Notes data.
- **What I changed by hand and why:** I supplied the demo identities and profile fields. These known passwords are for local demo use only and must not be seeded into a live database.
- **How I checked it works:** `php artisan migrate:fresh --seed` completed. Tinker showed admin, treasurer, and member roles (plus the existing demo users) and returned `"S10001"` for the demo member's profile. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `56763ad`

---

### Step 42: Add the MonthlyDues factory

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  The requested seeder calls MonthlyDues::factory(), but no MonthlyDuesFactory exists. Add a factory with a member profile, month, year, and required amount so the seeder can create dues records.
  ```
- **What the AI produced:** Added `MonthlyDuesFactory` with a related member profile, a generated month, the current year, and the default dues amount.
- **What I changed by hand and why:** I added this missing prerequisite because the requested seeder depends on `MonthlyDues::factory()->for($profile)`.
- **How I checked it works:** The factory was exercised during `php artisan migrate:fresh --seed`; the seeder created 33 dues records across 11 profiles.
- **Commit:** `a3df024`

---

### Step 43: Seed members with monthly dues

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Preserve the demo member profile as $demoProfile, create ten additional profiles, and seed September, October, and November 2026 dues for each profile using MonthlyDuesFactory. Rebuild the local database, verify counts and months, run the tests, commit the seeder, and log the hash.
  ```
- **What the AI produced:** Updated `DatabaseSeeder` to create 10 additional profiles and three explicitly dated 2026 dues records for each profile, including the demo member.
- **What I changed by hand and why:** I specified the dues months and year so records are deterministic and don't collide on the profile/month/year unique key. Existing Notes seeding remains unchanged.
- **How I checked it works:** `php artisan migrate:fresh --seed` completed. Tinker returned 11 profiles, 33 dues records, months `9`, `10`, `11` for `S10001`, and 0 payments. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `5b7e2f0`

---

### Step 44: Seed payments with allocations and donations

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  For each dues record, seed a payment recorded by the treasurer and allocate 30, leaving every third member's November unpaid. Every second member's October payment is 50; create a donation for any amount above the 30 allocation. Rebuild the local database, verify payment/donation/allocation/unpaid counts, run the tests, commit the seeder, and log the hash.
  ```
- **What the AI produced:** Updated `DatabaseSeeder` to create payments, allocate 30 to each paid month, and record donations for overpayments without carrying the excess into another month.
- **What I changed by hand and why:** A donation is the positive remainder after subtracting the amount applied to dues from the total payment. Here, a 50 payment allocates 30 to October and records the remaining 20 as a donation; it does not reduce a later month's dues.
- **How I checked it works:** `php artisan migrate:fresh --seed` completed. Tinker returned 29 payments, 6 donations, 29 allocations, and 4 dues records without payments. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `e210eb0`

---

### Step 45: Seed announcements

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Seed three published announcements and one draft, all authored by the seeded admin. Rebuild the local database, verify total, published, and admin-authored counts, run the tests, commit the seeder, and log the hash.
  ```
- **What the AI produced:** Updated `DatabaseSeeder` to create three published announcements and one draft, explicitly using the seeded admin as author.
- **What I changed by hand and why:** I specified the admin author and draft title. Supplying the author ID prevents the factory from creating a separate admin for each announcement.
- **How I checked it works:** `php artisan migrate:fresh --seed` completed. Tinker returned 4 announcements, 3 with `published_at`, and 4 announcements for the admin. `php artisan test` passed all 16 tests (55 assertions).
- **Commit:** `a03b89c`

---

### Step 46: Update the PRD data model for the payment-dues pivot

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Replace PRD sections 5.1 and 5.2 with the current models and relationships: include User and its role; MemberProfile soft deletion; Payment's member_profile_id and recorded_by_user_id; PaymentMonthlyDues with payment_id, monthly_dues_id, and allocated_amount; Donation; Announcement with author_user_id; and the payment-to-monthly-dues many-to-many relationship.
  ```
- **What the AI produced:** Updated the proposed model table and relationship list to document the current payment, dues-allocation, donation, and announcement schema.
- **What I changed by hand and why:** I replaced Payment's outdated `monthly_dues_id` and `dues_portion` fields with `member_profile_id` and documented the new `PaymentMonthlyDues` pivot keys and `allocated_amount`. I also added User role fields, MemberProfile's `deleted_at`, and the author/payment fields for Donation and Announcement, and updated the relationships to show the many-to-many dues allocation. The pivot's `allocated_amount` records how much of a payment applies to a particular dues month, allowing a payment to be split across months and dues totals to be distinguished from excess donations.
- **How I checked it works:** Compared the documented field and foreign-key names against the users, member_profiles, monthly_dues, payments, payment_monthly_dues, donations, and announcements migrations. The VS Code preview attempt was interrupted by active user input.
- **Commit:** `3d0049c`

---

### Step 47: Update the PRD donation rules and payments endpoint

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Update PRD section 5.3 to explain how payments are allocated across selected dues months and how excess becomes a donation. Update the POST /api/payments row with its request fields and add the 201 response shape. Compare the rules with the seeder, preview the PRD, commit the change, and log the hash.
  ```
- **What the AI produced:** Documented the outstanding-balance allocation, donation remainder, monthly paid/unpaid status, transaction behavior, and payment request/response in the PRD.
- **What I changed by hand and why:** For each selected month, subtract existing allocations from its required dues and allocate only what is still owed, up to the payment amount left. After processing the selected months, any payment remainder becomes a donation and is not carried into another month.
- **How I checked it works:** `git diff --check` passed. Compared the rules with `DatabaseSeeder`: a 50 payment allocates 30 to dues and records the remaining 20 as a donation. Opened the VS Code preview, but navigating to the edited sections was interrupted by active user input, so their rendered appearance was not visually confirmed.
- **Commit:** `f599895`

---

### Step 48: Add POST /api/register to the API plan

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add a public POST /api/register endpoint to PRD section 6.1, documenting the member-only role and request fields. Add the registration/login rate-limit note and explain what happens to a new registrant's member profile. Preview, commit, and log the change.
  ```
- **What the AI produced:** Added the registration endpoint, rate-limit and account-creation note, and new-registrant profile behavior to the PRD.
- **What I changed by hand and why:** I chose Option A: a new registrant has no MemberProfile until an officer or treasurer creates one. This keeps public registration limited to login-account details and leaves member-profile creation with the club staff who manage membership information.
- **How I checked it works:** `git diff --check` passed. Confirmed the registration row appears above login in section 6.1 and the rate-limit and profile notes follow the table. The VS Code preview was open, but navigating to section 6.1 was interrupted by active user input, so the rendered section was not visually confirmed.
- **Commit:** `b5510e9`

---

### Step 49: Add error behavior for each business rule

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add an error-behavior table to PRD section 4 for invalid access, payment fields, dues selections, partial payments, already-paid months, excess donations, and client-supplied allocation amounts. Decide how partial and already-paid months behave, check the table against the migrations and section 5.3, preview, commit, and log the change.
  ```
- **What the AI produced:** Added the business-rule response table and clarified in section 5.3 that selecting an already-paid month rejects the request.
- **What I changed by hand and why:** I chose to accept partial payments because several allocations can add up to the monthly requirement while the month remains unpaid. I chose to reject selecting an already-paid month rather than turn a duplicate dues selection into a donation; a payment must target dues that are still outstanding.
- **How I checked it works:** `git diff --check` passed. Compared the documented payment, dues, pivot, and donation fields with their migrations, and confirmed the partial-payment and paid-status rules agree with section 5.3. The VS Code preview was open, but scrolling to the new table was interrupted by active user input, so its rendered appearance was not visually confirmed.
- **Commit:** `55a1123`

---

### Step 50: Expand the role permissions table in the PRD

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Replace the permissions table in PRD section 2 with detailed role permissions, 401/403 behavior, and ownership/profile notes. Decide whether treasurers can manage members, whether members can edit student_id, and whether admins can prepare financial reports. Correct the registration note to match available endpoints, compare permissions to section 6, preview, commit, and log the change.
  ```
- **What the AI produced:** Expanded the permissions matrix and aligned the profile, dues, payment, member-removal, and announcement endpoint descriptions with it. Clarified that staff accounts are created by seeding only.
- **What I changed by hand and why:** I kept Treasurer access to add and edit members because the club's existing permissions and member endpoints already allow it. Members cannot edit their own `student_id` because it identifies the membership record and should be set by an officer. Admins cannot prepare financial reports so access to reports stays limited to the Treasurer.
- **How I checked it works:** `git diff --check` passed. Compared the permissions with the endpoint tables, including the all-authenticated own-profile/history/dues endpoints, admin-only soft deletion and draft access, and treasurer-only reports. The VS Code preview showed the updated table and notes rendered correctly.
- **Commit:** `65381d4`

---

### Step 51: Expand acceptance criteria with failure cases

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Replace the acceptance criteria in PRD section 3 with detailed success, validation, authorization, privacy, pagination, filtering, and empty-report cases for US01-US10. Decide whether admins can view payments and whether lists use 10 per page. Compare the criteria with the permissions and endpoints, preview, commit, and log the change.
  ```
- **What the AI produced:** Expanded the acceptance criteria for all ten user stories with response codes, validation outcomes, access limits, payment/dues behavior, pagination, and filtering.
- **What I changed by hand and why:** I kept Admin access to the payment list because the permissions table and `GET /api/payments` both allow it. I chose 10 records per page because it matches the starter Notes example and will be consistent when I build the controllers.
- **How I checked it works:** `git diff --check` passed. Compared the criteria with the permissions table and API endpoint access, including Admin payment-list access and Treasurer-only reports. The VS Code preview displayed the nested acceptance list correctly.
- **Commit:** `5b02b84`

---

### Step 52: Expand frontend screens with roles and API calls

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Replace the Main Screens list in PRD section 7 with a role-aware screen table, API calls, navigation behavior, loading/empty/error states, and token handling. Decide token storage, dashboard scope, and whether Admin records payments. Compare every call with section 6, preview, commit, and log the change.
  ```
- **What the AI produced:** Replaced the short screen list with an 11-screen table and documented role-based routing, request states, and token behavior.
- **What I changed by hand and why:** I chose in-memory token storage only to avoid persisting a bearer token; users must log in again after a refresh. I kept a simple dashboard for current-month status, totals, and announcements, leaving advanced charts for the level-up feature. Admin does not record payments because the permissions table and `POST /api/payments` reserve that action for the Treasurer.
- **How I checked it works:** `git diff --check` passed. Compared each screen's API calls and role with section 6; Admin and Treasurer can use the own-history/dues endpoints when they have a member profile. The preview was open on the API endpoint tables, but scrolling to the new section 7 table was interrupted by active user input, so that table was not visually confirmed.
- **Commit:** `257e638`

---

### Step 53: Pick level-up features from the menu

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Replace PRD section 8 with menu features for reports and charts, scheduled dues reminders, and frontend tests, totaling 20 points. Decide whether this mix fits and how reminders should reach members. Preview, commit, log the change, and check whether the host can run Laravel's scheduler.
  ```
- **What the AI produced:** Replaced the level-up feature table with Reports and charts (6 points), Queued jobs or scheduled tasks (8 points), and Frontend tests (6 points). Removed CSV/PDF exports unless approved by the instructor.
- **What I changed by hand and why:** I kept this 20-point feature mix because it uses the full available points for reports, reminders, and frontend tests. Reminders will be in-app only; no email or SMS integration is planned.
- **How I checked it works:** `git diff --check` passed. `php artisan schedule:list` reported no scheduled tasks yet. The local Windows Task Scheduler service is running and `schtasks.exe` is available, so local scheduling is possible. `DEPLOYMENT.md` does not identify a deployment provider, so deployed scheduler support still needs confirmation. Preview navigation to section 8 was interrupted by active user input, so I could not visually confirm the table.
- **Commit:** `c25be5e`

---

### Step 54: Name your 3 full-CRUD resources and the nested endpoint

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Update the PRD API plan so members, announcements, and monthly dues have full CRUD; keep payments create/read only; add a nested member-payments endpoint and query options for index routes. Align the dues permissions row, decide the three related business questions, preview, commit, and log the change.
  ```
- **What the AI produced:** Updated the member, monthly-dues, and payment endpoint tables; added the nested member-payments route and index query-options table; added the monthly-dues management permission.
- **What I changed by hand and why:** I kept payments append-only so the original financial record and its allocations remain auditable; any correction should use a future reversal workflow. The Treasurer creates dues records one member at a time to keep the action controlled and avoid unintended bulk changes. Only Admins can soft-delete members, preserving their payment history while limiting destructive account actions.
- **How I checked it works:** `git diff --check` passed. Compared the endpoint names, access, and own-record routes with the permissions table. VS Code preview navigation and focus were interrupted by active user input, so I could not visually confirm the rendered tables.
- **Commit:** `cdd14f6`

---

### Step 55: Correct PRD changelog dates to match commit history

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Replace the whole PRD section 10 changelog with the provided milestone summary, checking every date against git history. Commit the PRD change and log the hash.
  ```
- **What the AI produced:** Replaced the section 10 table with the assignment start, initial PRD/design work, database model and seeders, payment pivot/allocation changes, API and screen planning, level-up/full-CRUD planning, and ERD update.
- **What I changed by hand and why:** I changed the proposed November 16 and 17 dates for implementation and API-plan work to November 24 because the relevant commits are dated November 24. I used November 12 and 15 for the earlier assignment and planning work, replacing the older changelog dates that did not match the repository history.
- **How I checked it works:** Compared the milestones with `git log --format='%ad %s' --date=short`; `git diff --check` passed.
- **Commit:** `5c53b63`

---

### Step 56: Add AnnouncementPolicy

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add AnnouncementPolicy so any authenticated user can list announcements, published announcements are visible to all, drafts are admin-only, and only admins can create, update, or delete. Verify the Gate decisions in Tinker, run the test suite, commit, and log the change.
  ```
- **What the AI produced:** Added the automatically discovered `AnnouncementPolicy` and focused feature tests for list, visibility, and write permissions.
- **What I changed by hand and why:** I kept drafts hidden from non-admins so only admins can preview unpublished announcements; other users should see an announcement only after it has been published.
- **How I checked it works:** Tinker returned `true`, `false`, `false`, `true` for the requested Gate checks. `php artisan test` passed all 19 tests. Pint and `git diff --check` passed.
- **Commit:** `760346a`

---

### Step 57: Add AnnouncementResource

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add AnnouncementResource with an explicit JSON field list, an is_draft flag, and author id/name only when the author relation is loaded. Verify loaded and unloaded author serialization in Tinker, run the tests, commit, and log the change.
  ```
- **What the AI produced:** Added the announcement JSON resource and a feature test covering conditional author inclusion and email omission.
- **What I changed by hand and why:** I kept the author behind `whenLoaded` so serializing an announcement without eager loading does not trigger a lazy load or N+1 query. The API only includes the author's id and name when the relation is intentionally loaded.
- **How I checked it works:** Tinker showed the loaded author's id and name without email, and omitted the `author` key when the relation was unloaded. `php artisan test` passed all 20 tests. Pint and `git diff --check` passed.
- **Commit:** `ff3dd51`

---

### Step 58: Add both Form Requests for announcements

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add StoreAnnouncementRequest and UpdateAnnouncementRequest. Authorize through AnnouncementPolicy, validate title, message, and published_at, and let updates modify only submitted fields. Verify the store rules in Tinker, run the tests, commit, and log the change.
  ```
- **What the AI produced:** Added store and update Form Requests with policy authorization and the requested validation rules.
- **What I changed by hand and why:** I kept `author_user_id` out of both requests so the controller can assign the author from the authenticated admin rather than trusting client input. The update rules use `sometimes` so a request can change just one field.
- **How I checked it works:** Tinker returned `false` for valid store input and `["title"]` for invalid input. `php artisan test` passed all 20 tests. Pint and `git diff --check` passed. Update authorization will be exercised after its route is added in the next step.
- **Commit:** `4259224`

---

### Step 59: Add AnnouncementController and apiResource routes

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add AnnouncementController with paginated listing, draft filtering for non-admins, policy checks, eager-loaded author resources, and CRUD actions. Register its apiResource inside the Sanctum group, verify the five generated routes, run tests, and record the commit hash.
  ```
- **What the AI produced:** Added the announcement controller and five authenticated API resource routes.
- **What I changed by hand and why:** I kept the draft filter in the index query because `viewAny` only authorizes access to the list; it does not automatically apply `view` to each row. Filtering at the query prevents drafts from being included in non-admin responses.
- **How I checked it works:** `php artisan route:list --path=api/announcements` showed the expected five routes. `php artisan test` passed all 20 tests. Pint and `git diff --check` passed.
- **Commit:** `83d4fb4`

---

### Step 60: Add the announcement tests

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add the supplied AnnouncementApiTest feature tests for authentication, admin-only writes, validation, author assignment, draft visibility, response privacy, update/delete behavior, and missing records. Run the focused and full test suites, commit the test file, and record the hash.
  ```
- **What the AI produced:** Added 13 API feature tests covering announcement access, CRUD behavior, validation, draft filtering, and response fields.
- **What I changed by hand and why:** I kept the supplied assertions intact so failures would expose problems in the policy, requests, resource, or controller rather than being hidden by weakened tests.
- **How I checked it works:** `php artisan test --filter=AnnouncementApiTest` passed all 13 tests (40 assertions). `php artisan test` passed all 33 tests (109 assertions). Pint and `git diff --check` passed.
- **Commit:** `771ccb1`

---

### Step 61: Remove the Notes example API layer

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Remove the Notes example controller, requests, resource, policy, and tests. Remove its import and route, run the tests, and commit this as Commit A without rewriting history.
  ```
- **What the AI produced:** Removed the six Notes API files and its authenticated resource route. Updated the README to describe the announcement API and current feature tests instead of the removed Notes example.
- **What I changed by hand and why:** I removed the obsolete example-route comment and updated the README because it would otherwise refer to deleted Notes files and an outdated test count.
- **How I checked it works:** `php artisan route:list --path=api/notes` found no matching routes. `php artisan test` passed all 25 remaining tests.
- **Commit:** `841586a`

---

### Step 62: Remove the Notes data layer and seeder data

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Remove the Note model, factory, and migration; remove the notes() relationship and Notes seeder data. Keep the HasMany import, rebuild the local database with migrate:fresh --seed, run the tests, and commit separately as Commit B.
  ```
- **What the AI produced:** Removed the Notes model, factory, and migration; deleted the `notes()` relationship and Notes seeding.
- **What I changed by hand and why:** I kept the `HasMany` import because User still has an announcements relationship. I retained the Demo User account used by the README, while removing the Notes records created for it.
- **How I checked it works:** `php artisan migrate:fresh --seed` succeeded on the local SQLite database. Tinker confirmed the `notes` table is absent and four announcements are seeded. `php artisan test` passed all 25 tests. Pint passed for the seeder and routes; the `User.php` check reports pre-existing formatting issues, which I left unchanged.
- **Commit:** `b5a9f4c`

---

### Step 63: Add the PaymentService (course Step 47)

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Add PaymentService to validate selected dues, allocate payment amounts in satang without exceeding outstanding balances, reject already-paid months, save excess as donations, and wrap the complete operation in a database transaction. Verify donation, allocation, rollback, and partial-payment behavior; run tests; commit and record the hash.
  ```
- **What the AI produced:** Added the payment allocation service and focused tests for donations, partial payments, already-paid months, invalid member/month selection, and empty selection.
- **What I changed by hand and why:** I added a service-level rejection for an empty `monthly_dues_ids` array. The PRD requires at least one selected month, and without the guard the service could save a donation-only payment.
- **How I checked it works:** Tinker returned a ฿20 donation and ฿30 allocation from a ฿50 payment; a repeat for the paid month raised a validation error without increasing the payment count; a ฿10 partial payment had no donation. The four focused service tests passed, and `php artisan test` passed all 29 tests. Pint and `git diff --check` passed.
- **Commit:** `430b866`

---

### Step 64: Add tests for the PaymentService rules (course Step 48)

- **Date:** 2026-11-24
- **Tool:** Copilot
- **Prompt:**
  ```text
  Replace PaymentServiceTest with rule-focused tests for exact and excess payment allocation, multiple months, partial payments, already-paid months, member ownership, zero amount, transaction rollback, and treasurer attribution. Run focused and full tests, commit, and push.
  ```
- **What the AI produced:** Expanded `PaymentServiceTest` to nine focused tests with shared setup and payment/allocation helpers.
- **What I changed by hand and why:** I kept the requested checks for allocation totals, donation amounts, validation errors, unchanged database state after rejected payments, and the treasurer/member IDs associated with a payment.
- **How I checked it works:** `php artisan test --filter=PaymentServiceTest` passed all 9 tests (23 assertions). `php artisan test` passed all 34 tests (103 assertions). Pint and `git diff --check` passed.
- **Commit:** `3bdda27`
