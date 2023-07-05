# Changelog

## 0.1.185 (2023-07-05)

- Fix bunq API error in production

## 0.1.184 (2023-07-05)

- Fix bunq API error in production due to outdated client
- Update dependencies

## 0.1.183 (2023-06-15)

- Show message on transaction page to describe the transaction was delayed if it was made on an offline device
- Decrease purchase history separating from 6 to 2.5 hours

## 0.1.182 (2023-06-14)

- In purchase history list, separate purchases in chunks if more than 6 hours apart
- Increase number of items per page in purchase history from 25 to 50
- Add previous/next pagination buttons to purchase history list
- Fix kiosk showing incorrect search results if previous search was late
- Fix kiosk not handling empty searches properly
- Don't clear service worker caches on new release

## 0.1.181 (2023-05-04)

- Put page messages in a container, attached to the toolbar
- Fix style of messages breaking when multiple were shown after each other

## 0.1.180 (2023-05-02)

- Always show 'All purchases' button on bottom of bar page for administrators,
  even if there are no recent purchases
- Simplify style of action buttons on bottom of product and purchases list
- Improve red low balance message style, attach it to the toolbar on small
  screens
- Fix quick buy widget having different button text

## 0.1.179 (2023-05-01)

- Fix server error when user agent string of client is larger than 191
  characters. This happened on some Xiaomi Redmi phones and browsers.

## 0.1.178 (2023-04-29)

- Prevent committed kiosk transactions from replaying.
  This could potentially happen when an offline kiosk reconnects with an
  unstable connection to the server, causing purchases to be committed a
  second time.
- Show if and by how much time transactions are delayed, if they are synced
  later from an offline kiosk
- Show icons for kiosk and delayed transactions in transaction details and in
  transaction list
- Show less text in transaction/mutation lists to prevent text overflowing on
  very small or narrow devices
- Add details button to wallet balance label in main bar screen when user has
  positive balance

## 0.1.177 (2023-04-25)

- In kiosk, fix incorrect query encoding causing some search queries to break
- Remove support for searching products with ^ prefix
- Handle searches with an empty query better

## 0.1.176 (2023-04-24)

- Add offline support to kiosk mode. To use this, install the app as PWA. The app must connect to the internet at least once every month.
- Make kiosk user/product search more reliable, fall back to searching cached users/products on error
- Queue kiosk purchases if there is no connection, synchronize when connecting again
- Shorten kiosk buy button text to prevent overflowing when buying for multiple users
- Add kiosk specific service worker to handle offline mode and caching
- Fix precache/prefetch configuration of main service worker
- Fix rare error when buying products in kiosk mode

## 0.1.175 (2023-04-19)

- Fix kiosk layout on small screens by using less text in swap button
- Fix broken text on product edit page

## 0.1.174 (2023-04-07)

- Fix error when handling some bunq iDeal callback states
- Improve swap button icon in kiosk mode
- Improve action and swap button text in kiosk mode, make secondary action less vibrant
- Simplify confirm button text in kiosk mode

## 0.1.173 (2023-03-21)

- Fix issue where unrecognized manual payments were not refunded.
  This happened if the description had special characters, breaking SEPA
  payments due to horrendous requirements.

## 0.1.172 (2023-03-16)

- Fix potential security issue, where duplicate email addresses could be used
  with special characters in different casing

## 0.1.171 (2023-03-13)

- When confirming kiosk purchase and the view is darkened, cancel confirming by
  tapping the darkened view
- When kiosk green/red buy/cancel overlay is shown, close it early by tapping on
  it
- Increase kiosk green/red buy/cancel overlay time to 1.5 seconds
- Hint users to switch to a cellular connection on the HTTP 429 error page

## 0.1.170 (2023-01-13)

- Don't show leading ^ character in no search results message when searching
  users that start with the given query

## 0.1.169 (2022-12-20)

- Add alphabetical index for users in kiosk mode
- Prefix search query with ^ to find users with a name starting with that query
- Improve kiosk action buttons, use consistent look and placement

## 0.1.168 (2022-12-07)

- Add toggle to disable bar
- Prevent purchases on disabled bar, redirect to dashboard
- Disable kiosk for disabled bar

## 0.1.167 (2022-12-02)

- Use dark PWA, webmanifest and theme color when in dark mode
- Improve visuals and alignment of kiosk mode quantity selection modal
- Improve visuals and alignment of kiosk mode swap buttons
- Use blue selection color in kiosk mode
- Change kiosk mode glow effect to blue color
- Update dependencies

## 0.1.166 (2022-12-01)

- Change kiosk interface to be in dark mode

## 0.1.165 (2022-11-14)

- Block search engines from crawling sensitive links

## 0.1.164 (2022-06-24)

- Update bunq API to fix payment processing issues
- Update dependencies

## 0.1.163 (2022-06-23)

- Bump minimum PHP version to 8.1

## 0.1.162 (2022-06-23)

- Security fix: the transaction undo button for admins was usable by non-admins if the user had ownership of that transaction
- After economy creation, show currency presets to add, instead of showing the advanced currency creation screen
- Fix various typos
- Update dependencies

## 0.1.161 (2022-05-12)

- On the top-up page, inform the user of currently active/processing payments
- Show receipt subtotal below product list rather than below other cost entries, this should clarify that the subtotal is for products only.
- In kiosk mode, reset search fields on purchase or cancel
- In kiosk mode, prevent annoying browser auto complete menus in search fields
- In kiosk mode, show red cancel overlay in more situations
- Cancel bunq payment requests immediately to prevent double payments
- Fix some typos
- Update dependencies

## 0.1.160 (2022-03-30)

- In kiosk mode, make the swap column buttons more apparent and improve the used terminology
- In kiosk mode, remove obsolete success message after buying a product
- In kiosk mode, also revert columns to their default state on timeout

## 0.1.159 (2022-03-21)

- Show prices of exhausted products in grey label rather than blue
- In kiosk mode, add a button to users having items in cart to inspect its current contents
- In kiosk mode, choose better icons for the 'more' button in the product and user columns to better represent their function
- In kiosk mode, restyle swap columns hint as button rather than a hyperlink
- Improve email security banner showing contact information in case of an emergency, removed obsolete text
- Fix error when viewing bar member, when user has manually left encapsulating community
- Attempt to fix removed email notification in production environment, this message somehow isn't being sent in non-development environments
- Update dependencies

## 0.1.158 (2022-03-17)

- When an email address is removed, send a message to it as security notification
- Generate single magic link auth code, when authenticating through another session, don't change it every page reload as this was very annoying
- Improve description on HTTP 429 page, hint user on why this error may be shown
- Fix alignment of swap icons in kiosk view
- Fix incorrect text in password disabled mail

## 0.1.157 (2022-03-09)

- Add currency presets to economy screen, including EUR, USD, GBP
- On inventory move screen, show quantities of source inventory and list the source name on top of the page, rather than showing quantities of a random inventory
- Fix error when entering 0 as value in inventory fields
- Fix some mobile device keyboards not showing math characters in inventory input fields
- Fix typo in 'orphan wallet'

## 0.1.156 (2022-03-04)

- Add support for using math expression on inventory pages
- Fix top-up error when user enters custom amount with a comma rather than a dot
- Fix incorrect sorting in advanced buy widget
- Simplify verification email subject, it was too long

## 0.1.155 (2022-02-24)

- Greatly improve reliability of product exhaustion check logic, this now takes the product creation date, quantities of alternative inventory products and the type of recent manual inventory changes into account as well
- On the inventory screen, put the recent changes list in collapsible to prevent confusing it for a list of current item quantities
- Add product sub-total in receipt mail if non-products items are shown
- Define sorting in kiosk and advanced buy widget, prioritize registered members
- Mark unregistered users in gray with tag icon in advanced buy widget
- Show name of trashed products in purchase log, wallet stats and receipt mail, where it showed 'Deleted product' before
- Improve icon alignment in advanced buy widget, which caused layout resizing
- Improve finance report message box visuals
- Improve finance report terminology, rename outstanding to non-member
- Improve installation instructions, use Windows compatible commands
- Fix incorrect ordering and overlapping of some kiosk elements
- Fix incorrect admin creation instruction in README
- Fix broken back button on finance report page
- Fix finance report error with empty economy
- Fix some spelling mistakes

## 0.1.154 (2022-02-17)

- Hotfix for finance report error when there are no balance imports
- Rename financial report outstanding imports to unsettled imports

## 0.1.153 (2022-02-17)

Notable:

- Fully reimplement financial report feature, this now shows a more complete and cleaned interface classified in tabs, including: overview, members, orphaned wallets, unsettled balance imports
- Integrate unsettled amounts from balance import into financial report, these amounts are significant but not visible before
- Mark unregistered users in grey with a tag icon in kiosk mode, in an attempt to make it less likely to click on when a user is listed multiple times

Other:

- Add financial report button in bar manage screen
- Improve item sorting in various places, such as kiosk mode. Alphabetical
  sorting showed unexpected results when different capitalisation is used.
- Improve some terminology used in kiosk mode
- Improve styling of inline icons in kiosk mode
- Various pirate speak improvements

## 0.1.152 (2022-02-16)

- Fix email delivery errors, sending over SMTP hosts didn't work
- Fix payment processing errors, use correct dependencies along with bunq SDK
- Fix inventory balance error
- Update dependencies

## 0.1.151 (2022-02-15)

- Fix missing language resources in deployment

## 0.1.150 (2022-02-15)

Notable:

- Automatically verify email addresses when using it with a magic login link, this removes the extra manual verification step and should improve the verified email ratio
- When undoing an inventory change, ask to undo the related change as well, to fully undo a product move between two inventories
- Remove message sent to users after they verified their email address, it was more annoying than useful
- Update to Laravel 9, should improve overall performance

Other:

- Add icons to kiosk green/red success/abort screens
- Fix white border above dark modal backgrounds in kiosk mode
- Move kiosk success message to bottom, hide when showing cart buttons
- In balance update emails, show if the balance did not change since the previous update
- In balance emails, use better colors and styles for wallet buttons
- Reorder community/bar stats items
- Update dependencies

## 0.1.149 (2022-02-11)

- Enlarge wallet and top-up buttons
- Mark exhausted products in personal buy screen widget
- In kiosk mode, mark exhausted products with trash icon
- On inventory rebalance page, mark products having negative quantity, to recommend updating their quantities
- Mark advanced buy product selection with +1 rather than 1×, to make its behaviour consistent with kiosk mode
- Fix missing raw license file in production build
- Update placeholder fields in license
- Update laravel-mix manifest

## 0.1.148 (2022-02-10)

- Add kiosk inactivity dialog, asking user to continue with or reset cart if products were selected, rather than just resetting it
- Improve terminology used for kiosk deselect/clear/reset buttons
- Fix kiosk heartbeat not being triggered in various edge cases, making idle handling more reliable
- Improve kiosk column swap button visuals, style it as dedicated toolbar button
- Fix incorrect workbox cache configuration, which resulted in clients caching a lot of unused resources
- Bump laravel-mix, webpack, vue, improving client widget compatibility
- Bump workbox-webpack-plugin, improving client service worker compatibility and performance
- Bump flag-icon-css to flag-icons, improving language flag visuals
- Update browser database, improving client compatibility
- Resolve SASS compiler warnings
- Update dependencies

## 0.1.147 (2022-02-09)

- Add search field to community/bar member list
- Add interlinks to community/bar member detail pages for easier navigation
- Add list of all user wallets on community member details page
- Add list of user email addresses for admins on community/bar member details page
- Fix some pirate speak language item mistakes
- Remove unused language items
- Update bunq SDK
- Update dependencies

## 0.1.146 (2022-02-08)

- Add kiosk button to swap columns, enables a different buy mode to quickly purchase the same product for many users
- Various kiosk additions and improvements, such as a button to remove products in-cart for a single user
- Add button to undo a single inventory change
- Add recent changes list and index link to inventory page
- Add page for all changes in inventory
- Disable kiosk quantity modal animations for better performance on old devices
- Fix incorrect capitalisation on top-up redemption page

## 0.1.145 (2022-02-06)

- Fix error one some transaction detail pages, when initiated by another user
- Fix transaction details trying to show non-existent initiating user property

## 0.1.144 (2022-02-04)

- Fix kiosk error when economy has no transactions yet

## 0.1.143 (2022-02-04)

- Add wallet top-up page tailored to redemption, letting users top-up to zero
- List top-up redemption page on useful bar links page

## 0.1.142 (2022-02-04)

- Hotfix for error when sending balance update mail having deleted products on receipt

## 0.1.141 (2022-02-02)

- Don't suggest exhausted products in kiosk or user buy views
- Mark exhausted products in the kiosk view
- Give exhausted products a much lower sorting priority, anchor them to the bottom of lists
- Improve product suggestion algorithm, use more parameters, weigh-in transaction count
- Fix potential security vulnerability introduced in dependency
- Fix potential error when viewing inventory report while having unbalanced trashed products
- Change product exhaustion delay from 1 month to 1 week
- Improve language used in purchase receipts
- Fix various errors that may occur in configuration edge cases
- Update dependencies

## 0.1.140 (2021-12-03)

- Add user customizable member search tags to improve searchability
- Add current date as default balance import event label
- Shorten kiosk connection error banner

## 0.1.139 (2021-12-03)

- Hotfix for authentication error

## 0.1.138 (2021-12-03)

- Add receipt mail feature, user may opt-in in their email preferences
- Add receipt to balance update mail
- Add receipt hint to balance update and zero balance mail
- Improve internal money amount bag compatibility, support easy conversion
- In balance update email, separate each wallet in a block, improve plain text version
- Fix balance update mail history not limiting to its specific type
- Fix error when normalizing null price

## 0.1.137 (2021-11-30)

- Hotfix for error when buying products

## 0.1.136 (2021-11-30)

- Improve default from/to inventory selection when moving inventory products
- Inventory products now exhaust after 1 month
- Fix URL in password disabled email not being a hyperlink

## 0.1.135 (2021-11-16)

- Critical security patch, update doctrine/dbal to fix security issue
  (https://github.com/advisories/GHSA-r7cj-8hjg-x622)

## 0.1.134 (2021-11-06)

- Do not extrapolate wallet cost predictions because it shows unrealistic numbers
- Fix error when viewing product having trashed alternate inventory products
- Fix inventory error when buying some product combinations
- Fix incorrect inventory period report product unbalance volume percentage

## 0.1.133 (2021-11-05)

- Hotfix for error on inventory period report with in-balance products

## 0.1.132 (2021-11-05)

- Add separate inventories and bar inventory button to bar management page
- Add unbalance percentage of product volume in inventory period report
- Add week/month jump buttons to inventory time travel
- Collapse exhausted inventory products on add/remove/balance/move pages as well
- Fix incorrect success message when moving inventory products
- Fix accidental selections in kiosk mode
- Fix alternate inventory items not being cloned with a product
- Fix inventory quantity estimations and cost predictions suggesting huge numbers in very short periods

## 0.1.131 (2021-11-04)

- Fix inventory product monthly purchase estimate, properly extrapolate for short periods
- Fix wallet top-up monthly expense prediction, properly extrapolate for short periods

## 0.1.130 (2021-11-03)

- Add monthly purchase estimate and drain estimate to inventory products
- List purchase volume for all products in inventory period report
- Show product quantity sum on inventory index
- Fix inventory report not balanced warning not showing up in some cases
- Fix inventory period report showing empty stats in some cases
- Hide inventory quantities for trashed products

## 0.1.129 (2021-11-02)

- Fix error on inventory period report page when unbalance money amount is zero

## 0.1.128 (2021-11-02)

- Add page to set alternative inventory products for a product
- Show inventory quantities on product administration pages
- Further polish inventory period report page
- When deleting an inventory, suggest to move all contents to another inventory
- Inventory products are now exhausted when their quantity remained zero for at least 2 months
- Collapse exhausted inventory products by default, clearing up the inventory page

## 0.1.127 (2021-11-01)

- Fix various errors on inventory pages

## 0.1.126 (2021-11-01)

- Add mass product add/remove page to inventory
- Add mass move page to inventory
- Add inventory product details page
- Add inventory product changes index and details page
- Add inventory period report page, listing unbalanced products and other statistics
- Add inventory quantity time travel option
- Improve inventory rebalance reliability
- Improve error handling on inventory rebalance pages
- Suggest numeric inputmode keyboard for inventory rebalance fields
- Show bar/community member last visit time in member list
- Do not show property for user that created product if unknown

## 0.1.125 (2021-10-30)

- Emergency fix for bunq payment failures
- Don't show inventory exhausted products list if empty

## 0.1.124 (2021-10-29)

- Add mass product rebalance page to inventory
- Improve inventory quantity list grouping, sort by product name
- Use PHP 8.0 on CI
- Update dependencies

## 0.1.123 (2021-10-29)

- Add page to migrate balance import alias to different name/email

## 0.1.122 (2021-10-27)

- Add inventory management stubs, management pages are not yet implemented
- Add inventory tracking for purchased products
- Remove SOFORT mention on iDeal payments

## 0.1.121 (2021-10-20)

- Hotfix for error when user balance drops below zero

## 0.1.120 (2021-10-19)

- Add toggle to user email settings for receiving balance below zero notification
- Fix product change time not being updated when just its name or price is changed
- Shorten iDeal payment expiration from 2 weeks to 36 hours

## 0.1.119 (2021-10-15)

- Add search field to admin product list
- Sort products alphabetically and show price in admin product list

## 0.1.118 (2021-10-12)

- Fix economy delete errors, cascade delete to balance imports, mutations and members
- Add support to delete non-empty balance import systems
- Add delete confirmation to economies and balance import systems

## 0.1.117 (2021-10-04)

- Fix error when searching bar product catalog with no results
- Fix user creating or editing a product not being properly registered

## 0.1.116 (2021-09-28)

- Fix transaction details error when product is trashed
- Fix kiosk mode error when product is trashed
- Fix trashed products not showing up in purchase history export
- Fix user creating a product not being registered
- Remove redundant 'enabled' state for products, use trashing instead
- Update dependencies

## 0.1.115 (2021-09-17)

- Send users an email notification when their wallet balance drops below zero
- Be smarter about the default selected top-up amount
- Fix balance update mail translations being broken in English
- Fix payment service processing duration text inconsistency

## 0.1.114 (2021-09-16)

- Track users creating/modifying products, show in product details
- Add 'no connection' banner to kiosk mode
- Tweak top-up advance amount label text

## 0.1.113 (2021-09-15)

- Predict monthly user wallet cost, suggest 1 and 3 month advance deposits
- Do not color previous balance in balance update mail
- Add additional confirmation fields to dangerous economy wallet operations
- Enforce community administrator role for dangerous economy wallet operations

## 0.1.112 (2021-09-05)

- Add economy wallet operations page to zero/delete all member wallets
- Put bar kiosk buttons onto separate page
- Change order of buttons on app management page
- Simplify contact page
- Various locale improvements

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
