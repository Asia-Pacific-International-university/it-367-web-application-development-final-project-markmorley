# PRD: _your app's name_

**Student:** _your name_
**Version:** 1.0, _date_
**Due:** day 3 of week 1. Do not start building your own models until you have feedback on this document.

> Delete the hints in _italics_ as you fill in each section. Be specific to **your** problem: a PRD that could describe any app will be sent back.

---

## 1. Problem and User

The Filipino Club currently manages its membership information and dues payments using spreadsheets and manual records. This approach can make it difficult for club officers and the treasurer to keep track of which members have paid and which members still have outstanding dues. Payment records can also become scattered or difficult to find, making it time-consuming to prepare financial reports. In addition, members may have difficulty checking their own payment history. The Club Membership and Dues Management System will provide a centralized platform where club officers, the treasurer, and members can manage and access relevant membership and payment information more easily.

**Primary users:**

- **Club officers:** Manage club membership information and oversee membership and dues activities.
- **Treasurer:** Record and monitor dues payments, check unpaid balances, and prepare financial reports.
- **Members:** View their membership information and check their own payment history.

## 2. User roles

| Role | Who they are | What they can do | What they cannot do |
| --- | --- | --- | --- |
| _admin_ | | | |
| _member_ | | | |

## 3. User stories

_At least 10. Priority: **Must** (the app is useless without it), **Should**, or **Could**. Acceptance criteria say how you will know it works; each one should become a test._

| # | As a... | I want to... | So that... | Priority |
| --- | --- | --- | --- | --- |
| US1 | | | | Must |
| US2 | | | | |

**Acceptance criteria**

- **US1:**
  - _Given ..., when ..., then ..._
- **US2:**
  -

## 4. Business rules

_Rules the server enforces no matter what the frontend does. At least 2. Example: "A room cannot be booked twice for overlapping times: the API returns 422 with the message 'This room is already booked at that time.'"_

| # | Rule | Where it applies | What happens when it is broken |
| --- | --- | --- | --- |
| BR1 | | | |
| BR2 | | | |

## 5. Data model

_At least 5 of your own models. List each with its main fields and relationships. Include a first ERD in `docs/erd.png`._

| Model | Main fields | Relationships |
| --- | --- | --- |
| | | |

![ERD](docs/erd.png)

**Architecture:** _a simple diagram or list: Vue/React frontend, Laravel API, database, and where each will be hosted. Put the diagram in `docs/architecture.png`._

## 6. API plan

| Method | URL | Role | Purpose |
| --- | --- | --- | --- |
| POST | `/api/login` | guest | Log in and receive a token |
| | | | |

## 7. Frontend plan

**Framework:** _Vue.js or React (required by CLO 2), e.g. React with Vite_. **Why:** _one or two sentences_.

**Main screens:**

1. _Login_
2.

## 8. Level-up features

| Feature | Points | How it fits my app |
| --- | --- | --- |
| | | |

## 9. Out of scope

_What you will deliberately not build in 3 weeks._

-

## 10. Changelog

_Add a dated entry for every change to this plan after the Week 1 check-in._

| Date | What changed | Why |
| --- | --- | --- |
| | | |
