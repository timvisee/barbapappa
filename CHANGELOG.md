# Changelog

## 0.1.111 (2021-09-01)

- Add app management button to sidebar for app administrators
- Add `user:add` console command to add new (admin) users from the console
- Add changelog
- Move dashboard link to top of sidebar

## 0.1.110 (2021-08-24)

- Add reset button to bar user membership page
- Add colored action buttons to balance update mail
- Separate community economies in blocks as well in balance update email
- Tweak balance update mail styling
- Show nice error to user on top-up page if no payment method is configured
- Show useful warning and redirect for shared top up URL when user has no wallet
- Mark payment service withdrawal as currently not supported for admins
- Fix text alignment on generated posters
- Fix balance update mail showing previous balance with '1 month ago' even if it
  didn't exist for that long
- Fix various balance update mail styling errors when having multiple wallets
  and economies
- Fix error when inspecting payment service with disabled bunq account
- Update `dompdf` to fix poster generation with PHP 8
- Update dependencies

## 0.1.109 (2021-08-03)

- Emergency patch: prevent leaking sensitive details through debug output
  (https://gitlab.com/timvisee/barbapappa/-/issues/478)

## 0.1.108 (2021-08-03)

- Hotfix for payment status mail syntax error in PHP 8

## 0.1.107 (2021-08-03)

- Fix release build
- Bump PHP version requirement to 7.4, add missing package on CI

## 0.1.106 (2021-08-03)

- Prevent annoying scroll jumps when deselecting advanced buy page items,
  prevent widget from shrinking once grown
- Require bar user role to view product index/catalog
- Update dependencies

## 0.1.105 (2021-07-29)

- Fix purchase/payment export end date being exclusive, rather than inclusive
- Fix export date ranges not aligning to begin/end of day

## 0.1.104 (2021-07-29)

- Fix error on product page if user is not a bar member
- Fix Dutch typo
- Redirect all bar pages to info page if user is not a bar member

## 0.1.103 (2021-07-13)

- Add visual cues to kiosk mode, darken interface when confirming, show
  green/red screen after buy/cancel
- Fix economy payments page showing payments in wrong order
- Improve file export performance
- Link finance report in-progress amount to payments page
- Denote absolute dates as being in UTC
- Various translation fixes

## 0.1.102 (2021-07-11)

- Add page to view all payments in community, listing all user payments handled
  by Barbapappa
- Add page to export bar purchase history
- Add page to export community payments
- Add quick-share buttons on mobile to 'Useful links' pages
- Add optional _Reply-To_ address field when sending balance import email
- Allow community administrators to inspect member payments
- Improve session management terminology
- Fix links on about page

## 0.1.101 (2021-07-07)

- Show pending transaction/mutation/payment amounts in yellow
- Always show current user in advanced buy widget even if hidden, but hide for
  other users. This modifies logic implemented in the previous release.

## 0.1.100 (2021-07-07)

- Fix current user being visible in advanced buy widget even if the user choose
  to be invisible

## 0.1.99 (2021-07-04)
## 0.1.98 (2021-06-30)
## 0.1.97 (2021-06-28)
## 0.1.96 (2021-06-25)
## 0.1.95 (2021-06-22)
## 0.1.94 (2021-06-18)
## 0.1.93 (2021-06-17)
## 0.1.92 (2021-06-15)
## 0.1.91 (2021-06-11)
## 0.1.90 (2021-06-11)
## 0.1.89 (2021-06-10)
## 0.1.88 (2021-06-09)
## 0.1.87 (2021-06-09)
## 0.1.86 (2021-06-07)
## 0.1.85 (2021-06-03)
## 0.1.84 (2021-06-02)
## 0.1.83 (2021-06-02)
## 0.1.82 (2021-06-02)
## 0.1.81 (2021-06-02)
## 0.1.80 (2021-05-31)
## 0.1.79 (2021-05-26)
## 0.1.78 (2021-05-25)
## 0.1.77 (2021-05-21)
## 0.1.76 (2021-05-20)
## 0.1.75 (2021-05-17)
## 0.1.74 (2021-05-17)
## 0.1.73 (2021-04-12)
## 0.1.72 (2021-01-05)
## 0.1.71 (2020-12-02)
## 0.1.70 (2020-12-02)
## 0.1.69 (2020-12-01)
## 0.1.68 (2020-11-30)
## 0.1.67 (2020-11-25)
## 0.1.66 (2020-11-24)
## 0.1.65 (2020-10-26)
## 0.1.64 (2020-10-13)
## 0.1.63 (2020-09-20)
## 0.1.62 (2020-09-20)
## 0.1.61 (2020-09-17)
## 0.1.60 (2020-09-05)
## 0.1.59 (2020-08-26)
## 0.1.58 (2020-08-25)
## 0.1.57 (2020-08-25)
## 0.1.56 (2020-08-11)
## 0.1.55 (2020-08-11)
## 0.1.54 (2020-08-11)
## 0.1.53 (2020-08-11)
## 0.1.52 (2020-08-09)
## 0.1.51 (2020-08-09)
## 0.1.50 (2020-08-07)
## 0.1.49 (2020-08-07)
## 0.1.48 (2020-08-07)
## 0.1.47 (2020-07-31)
## 0.1.46 (2020-07-30)
## 0.1.45 (2020-07-30)
## 0.1.44 (2020-07-30)
## 0.1.43 (2020-07-26)
## 0.1.42 (2020-07-16)
## 0.1.41 (2020-07-12)
## 0.1.40 (2020-07-10)
## 0.1.39 (2020-07-10)
## 0.1.38 (2020-07-07)
## 0.1.37 (2020-07-03)
## 0.1.36 (2020-04-30)
## 0.1.35 (2020-04-20)
## 0.1.34 (2020-02-19)
## 0.1.33 (2020-02-11)
## 0.1.32 (2020-01-24)
## 0.1.31 (2020-01-20)
## 0.1.30 (2020-01-06)
## 0.1.29 (2020-01-06)
## 0.1.28 (2020-01-03)
## 0.1.27 (2019-12-20)
## 0.1.26 (2019-12-18)
## 0.1.25 (2019-12-15)
## 0.1.24 (2019-12-15)
## 0.1.22 (2019-12-04)
## 0.1.21 (2019-12-04)
## 0.1.20 (2019-12-04)
## 0.1.19 (2019-12-01)
## 0.1.18 (2019-11-27)
## 0.1.17 (2019-11-27)
## 0.1.16 (2019-11-26)
## 0.1.15 (2019-11-26)
## 0.1.14 (2019-11-26)
## 0.1.13 (2019-11-26)
## 0.1.12 (2019-11-26)
## 0.1.11 (2019-11-26)
## 0.1.10 (2019-11-23)
## 0.1.9 (2019-11-22)
## 0.1.8 (2019-11-21)
## 0.1.6 (2019-11-19)
## 0.1.5 (2019-11-17)
## 0.1.4 (2019-11-16)
## 0.1.3 (2019-11-16)
## 0.1.2 (2019-11-16)
## 0.1.1 (2019-11-15)
## 0.1.0 (2019-11-15)