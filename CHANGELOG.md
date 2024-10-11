<details>
<summary><h2>Changelog</h2></summary>
<details>
<summary><h3>1.2.2</h3></summary>
<h4>1.2.2 - Auditing fixes</h4>

- Removed the nav button dimming on the favourites page.
- Fixed the audit.php page not working due to a missing csrf token.
- Added a JS scroll feature for anchor tags to head.js to stop the content being hidden under the banner.
- Updated all git references "GitLab" -> "GitHub"
- New README format.
- Changelog moved to CHANGELOG.md

</details>
<details>
<summary><h3>1.2.1</h3></summary>
<h4>1.2.1 - Quality of life changes</h4>

- Added FontAwesome icons to the burger menu list to clearly display what each link does
- Changes some css to allow the burger menu to show correctly with the new changes
- Added a favourites table to DB
- Added a favourites star icon below the banner
- Made the banner position:fixed to stop it moving down the page
- Added a function to add/remove a favourite using AJAX when the favourite star is clicked
- Added includes/favourites.inc.php page to do the favouriting logic
- Added a favourites section on the user burger menu
- Can remove favourites from the favourites.php page
- Split the stock and favourites JS into separate files
- Adjusted a bunch of pages to work with the new fixed banner.
- Favourites button is now coloured to the general banner colour.

</details>
<details>
<summary><h3>1.2.0</h3></summary>
<h4>1.2.0 - Mobile Form Factor and additional fixes</h4>

- Fixed the burger menu on the nav to show the containers page and fit on the screen correctly.
- Fixed the 2FA issuer being 'testing' to now getting the system name from the DB.
- Fixed the 2FA prompt showing weird on mobile.
- Changed the login_log ipv4 data type from int(11) to bigint(11) to allow for public ips that exceed the int limits.
- Changed the login_failure ipv4 data type from int(11) to bigint(11) to allow for public ips that exceed the int limits.
- Changed the session_log ipv4 data type from int(11) to bigint(11) to allow for public ips that exceed the int limits.
- Changed the bypass_2fa ipv4 data type from int(11) to bigint(11) to allow for public ips that exceed the int limits.
- Moved JS from the standard php files to the assets/js folder where possible and sourced them in.
- Added a "Add New" button to the manufacturer select on the stock page.
- Fixed the SKU patterns on the stock-*.inc.php pages.
- Fixed a bunch of meta tags and preloading in the head.php
- Hopefully finally fixed the theme_id error received when a user first logs in

</details>
<details>
<summary><h3>1.1.2</h3></summary>
<h4>1.1.2 - Bug fixes</h4>

- Fixed the stock page showing duplicate items based on manufacturer.
- Removed the email address prompt on the login page.

</details>
<details>
<summary><h3>1.1.1</h3></summary>
<h4>1.1.1 - 2FA Cookie fixes</h4>

- Bypass_2fa now uses randomly generated cookies and cookie names, stored in the DB
- Adjusted login scripts to check for the new cookies.
- Secret now removed from the form on the 2FA input and moved to the SESSION array.
- 2FA functions now more secure and no hidden info left on the page.
- Fixed the login error output not showing correctly by adding some ajax to query the responsehandling inc page.

</details>
<details>
<summary><h3>1.1.0</h3></summary>
<h4>1.1.0 - 2FA Integration</h4>

- Added CSRF token checking to login.inc.php. This was missing.
- Added ids to login.php inputs
- Changed all of the Location headers in the login.inc.php to be returns for ajax.
- Added the Google Authenticator package
- Added the Google Authenticator url to the meta tag
- Added login.js for all the login js bits
- Added 2fa.inc.php for the 2fa bits
- Reconfigured the login.inc.php page to fully work via AJAX and check for 2FA settings.
- mysqldump of new changes added.
- Changes noted in the update bash file
- Admin page now shows larger tables better
- Admin page now has an authentication section for toggling 2FA globally.
- All swipe card code has been commented out as this is likely not going to be used. Can be removed later.
- Fixed the success message on the addlocaluser page
- Profile page now allows users to toggle 2FA except for the root user. This is actioned by admin.inc.php
- Profile page allows you to reset your 2FA.
- Admin users table now has a reset 2FA option for resetting other users' 2FA
- Root user can no longer have 2FA prompts.
- 2FA prompts now submit on enter key.
- 2FA can now be "remembered" so you only have to input it once every 30 days on a device
- Resetting a local user password will clear all "remembered" 2FAs for the user

</details>
<details>
<summary><h3>1.0.1</h3></summary>
<h4>1.0.1 - Login history</h4>

- Added a login history to the profile page.
- Fixed the stock description showing the /r/n instead of line breaks on the stock edit text area and the stock main page.
- Image management section of the admin page, now loads images on an interval of 20 per button click to save loading times.
- Added a credential login checker on the db credentials to redirect to an error page if there is an issue.

</details>
<details>
<summary><h3>1.0.0</h3></summary>
<h4>Official 1.0.0 release.</h4>

- Removed all sensitive data from all versions.
- Fixed the profile page json stopping text input.
- Removed all references to affected_rows() due to deprecation.
- Fixed the smtp test page to allow no username and no password. Also now works with no ssl/tls.
- Allowed admins to save blank auth username/password to the db.
- Removed the system name from the subject of all email.
- Nav dropdown menu now opens on mouse over.
- Added notifications for adding, removing and moving optics.
- Updated the notifications table to add the new notifications.
- Added a type dropdown filter on the cables page.
- All time/date variable are now in the same format. 
- Any cost input now has a scale to 2 decimal places.
- Added changelog entries for logging in with LDAP.
- Fixed the csrf_token being missing on some admin user areas.
- Fixed the logout changelog entry to actually showing logout as the reason.

</details>
<details>
<summary><h3>0.7.2-beta</h3></summary>
<h4>Beta release 0.7.2, CSRF Token added, optic distance added.</h4>

- Added CSRF tokens and some slight changes to some files to make it work better.
- Added CSP policy meta header to head.php.
- Removed old AJAX/jquery references in head.php.
- Added an Anti-clickjacking header in head.php (in php).
- Used htmlspecialchars() on $_GET requests that print to the page to stop injection.
- Fixed the get-config php page to make the theme defaults strings not an array.
- Fixed the changelog not showing login failures/attempts.
- Added optic_distance table.
- Added distance_id to optic_item table.
- Added spectrum field to optic_item table to show wavelength.
- Added the logic for adding distances to the DB.
- Fixed the optics page to show the correct info.

</details>
<details>
<summary><h3>0.7.1-beta</h3></summary>
<h4>Beta release 0.7.1, Some script fixes and visual changes.</h4>

- Added a checker for any MySQL servers on the system before installing mysql. Uses the existing one if exists.
- Adding stock properties now correctly adds shelves.
- Fixed the stock image editing to make the images fit in the table better with a max height added
- Fixed the admin page user table to look nicer and less squashed. 
- Changed the padding on the buttons in the user table to look nicer
- Index page now only loads the non-deleted manufacturer/tags and in alphabetical order.
- Added a row count to the deleted stock under stock management in admin.php
- Ajax select boxes now order by name rather than id
- Removed the form elements from the new-properties page to stop it redirecting needlessly and breaking.
- Added some special character captures for the confirmAction on the stock removal page when deleting a stock object.
- Index manufacturer drop down now shows exact manufacturer matches instead of partial matches.
- Login log should now get the user id on login.
- Login page now encrypts the data sent on login form
- Login inc page no longer LDAP escapes the password. This was causing issues and was not necessary.
- Added csrf tokens based on an OWASP vulnerability. This is done in session.php.

</details>
<details>
<summary><h3>0.7.0-beta</h3></summary>
<h4>Beta release 0.7.0, Login tracking and blocking, containers and container logic.</h4>

- Added login_log table to track login attempts.
- Added login_failure table to track failed login count.
- Renamed sessionlog table to session_log.
- Added login_log_id to session_log table.
- New include file added for login tracking and blocking, as includes/login-functions.inc.php
- Adjusted the login.in, session.inc and logout php pages to accommodate the new login blocking and tracking.
- Fixed some LDAP testing bugs.
- "parent_id" field dropped from area table. This was unused.
- "is_container" field added to item table. This marks the item as a container.
- Containers link added to nav bar.
- containers.inc.php page added for the container logic.
- Containers can be added from the containers page.
- Stock add page now has asterisks marking required fields.
- Items can now be linked to and unlinked from containers
- Stock move page now shows the container the item is in. 
- Stock move page now warns you when moving stock that is within a container.
- Moving stock no longer deleted the previous one and adds a new copy. No idea why I did this...
- Removing stock page now only shows the serials of the selected manufacturer. This was missed before and it showed all for the shelf regardless of manufacturer.
- Container field added to the remove stock page and checks for the container the item is in for removal.
- Removing a container now prompts to remove/move the contents
- The remove page now shows what is and is not in a container.
- Containers page now shows the location of the container. The SQL query for this is rather large though, so might need to be changed at a later date.
- Stock page buttons are now inline with the Stock heading
- Removed all references to "cotnainer"...
- Can now remove children from containers on the containers page
- Can now link and unlink children from the stock page
- Can now add children on the containers page.
- Can now see containers which have no children on the containers page.

</details>
<details>
<summary><h3>0.6.0-beta</h3></summary>
<h4>Beta release 0.6.0, Optics stocking, Auditing and database renamed to stockbase.</h4>

- Optic modules now stocked under optics.php
- optics.php shows the list of optics in store for each site similar to how the index page shows the main stock.
- Comments can be added to the optics
- Searching for optics searches through all fields rather than just model.
- New tables added: optic_item, optic_connector, optic_type, optic_speed, optic_vendor, optic_comment, optic_transaction, stock_audit
- Due to new tables being added, there will need to be some SQL adjustments on updates/downgrades to this version
- users_roles table has a new field: is_optic
- Stock option added to the nav bar.
- Nav bar now highlights based on the page you are on.
- Nav bar links (right) are now a elements instead of button, so that middle click works.
- Version number is now pinned to the bottom right of the nav bar. This currently cannot be hidden. This will be removed come version 1.0.0
- All logic added for the optics page. Can now add/remove optics and comments, and add vendors and types.
- Profile link is now named 'Profile' in the navigation. Now that there are more links, this is clear.
- Optic Attribute Management is now included on admin page to manage vendors, types and connectors.
- Changelog now works with optic tables
- Database now named stockbase
- Update script adjusted for all the changes.
- IndexAjax is now using a CTE table to make things faster on large datasets.
- Stock Add/Remove/Move pages updated with new CTE table to speed things up.
- Add New Stock button on the Stock Add page now fills in the name with whatever was in the search box.
- Pagination has been adjusted on all pages for allowing over 5 pages.
- Cablestock now listed in the nav bar as "cables".
- Item stock button removed from cablestock.
- Cables button removed from index.
- Comments button on optics is now the message icon with a number for the count inside.
- Show/Hide deleted optics now possible. Can also restore them.
- Added Dark Black theme.
- Admin, Profile and Logout buttons moved from nav to "username" dropdown in top right corner.
- Renamed indexajax.php to stockajax.php
- Add/Remove/Move stock pages now load the content using js and ajax - the same as the index page.
- Audit page added, which has a 6 month date retention on it, meaning if the last date was 6 months ago, it will show on the audit page.
- Pagination added to optics and cablestock pages to match the other stock pages.
- Added DOCTYPE to all pages that need it to remove Quirks Mode issues.
- Corrected the ldap-test script to actually filter based on input.
- Added a border to the footer using the background colour to all css files.
- Added LDAP injection prevention on the login page.

</details>
<details>
<summary><h3>0.5.0-beta</h3></summary>
<h4>Beta release 0.5.0, Session logging and management for users, changelog improvements and some formatting.</h4>

- Added sessionlog table to database.
- sessionlog table tracks the login/logout/timeout/expiry of user sessions to manage their login time.
- New file: includes/session.inc.php added. This manages the sessions with new functions.
- session.php manages the session.inc.php page on each web page accessed.
- Update script adjusted to allow the database changes.
- Admin page now has a "Session Management" section to kill any inactive or suspicious sessions.
- Admin sections moved around to be more logical
- Changelog page now has onclicks to show a hidden row with the table data for the record_id
- Some table formatting changes to the move hidden rows. These are now cantered
- Fixed the assign card buttons causing instant errors and not working on profile page
- Added changelog filters to the changelog page. This allows time frames and table/user filtering.

</details>
<details>
<summary><h3>0.4.2-beta</h3></summary>
<h4>Beta release 0.4.2, Update script web server checking and feedback updates.</h4>

- Install script now checks which web servers are installed and asks which to use and whether to disable the other if there are multiple.
- If only one web server is installed, it uses it by default. This will be apache2 if no web server was installed initially, due to PHP installing apache2.
- Update script updated to accommodate 0.4.0-beta and 0.4.1-beta. 0.4.1-beta and 0.4.2-beta are the same.
- Manufacturer can now be changed on a per item basis under the stock page.
- Stock row editing save button now update to 'update'
- Remove button added to populate the remove form and the logic to go with this in JS
- Stock rows are now outlined in dark when selected to make it more obvious
- Themes updated with the .highlight class
- Index table and cablestock table now updated with each row having the highlight class
- Tags are now removed from the stock rows on the stock page. This is related to the stock object, not the items.
- Tags now have an X icon on them when editing stock. This is removed when the tag is removed, along with the clickable class.
- Tags edit box is now larger and allows wrapping
- Tags on the index stock table allow wrapping to stop the table exceeding the width limits.
- MySQL queries now allow for single quotes and double quotes on string entries. This is also formatted correctly on SELECTs.
- Index page stock name is now a link instead of onclick to allow middle mouse clicks.
- Moving cable stock is now possible from the cablestock.php page. This will also be possible from stock.php soon.
- Tags page now has the correct table highlighting on selecting rows.
- Footer can now be disabled/enabled in the admin page under the Footer section. 
- DB tables: config and config_default have 3 new columns.
- Can now Add/Remove/Move cablestock from the stock.php page. This now loads the correct info and fields.

</details>
<details>
<summary><h3>0.4.1-beta</h3></summary>
<h4>Beta release 0.4.1, Cost toggles and quality of life changes.</h4>

- Fixed some page redirects for the edit stock page. Now diverts you to the stock main page if all is successful, else drops you back on the edit page.
- Cablestock description is now optional. This is not always relevant to the item.
- Stock.php now has response handling built in. This means that error messages will show correctly.
- LDAP settings on the admin page now has the correct error checking and response handling. There are a couple of unique ones left in place.
- Can now disable / enable the cost for items. This is not always needed so can be toggled off under stock management in admin.php.

</details>
<details>
<summary><h3>0.4.0-beta</h3></summary>
<h4>Beta release 0.4.0, Label to Tag.</h4>

- Renamed the stock_label and label table to stock_tag and tag. Moving away from the term 'label' as it is not a fit name.
- renamed the stock_tag table column 'label_id' to 'tag_id' to match the theme.
- Changed all references of label to tag in the codebase. 
- Added tags.php page to show all tags and their connections. This is not reachable without URL currently.
- Stock Locations in admin page now allows you to see deleted locations and restore them, similar to attributes.
- Adding properties is now an ajax request (e.g. adding tags, manufacturers, shelves areas and sites in the add new stock section). This means the page doesn’t refresh.
- Added description to the tag table for editing on the tags page.
- Stock edit script now separately checks for each change.
- Stock edit script now only removes the tags that are no longer linked.
- Stock edit script now only sends emails if there have been changes.
- Password reset modal div now works on mobile format.

</details>
<details>
<summary><h3>0.3.2-beta</h3></summary>
<h4>Beta release 0.3.2, Update scripts for version management and some small feature changes.</h4>

- Update script in place. Testing required for full version changing, but this will be more relevant when the database structure changes.
- Added Stock Management section to admin page. This allows you to recover/restore deleted stock objects instead of creating new ones.
- Added Attribute Management section to admin page. This allows you to delete and recover labels and manufacturers. This may extend in the future.
- Changelog event added to stock-new-properties.inc.php. This is for adding labels, manufacturers and locations.
- Added an impersonation feature for the root user only. This means the root user can become the user they select from the users list.
- Impersonation can be cancelled by clicking the button on the nav bar.
- Added new email notification for restoring deleted stock.
- Can now restore stock after deleting instead of re-creating the stock item again.
- Added responsehandler.inc.php page to handle errors/success responses from page redirects. This now means the file only need to be included on the page and a function placed where the output should be seen.
- Collected all current error messages hard coded into files and moved them to the response handler page.
- Stock page now shows items that are deleted. A new prompt shows up warning you it is deleted.
- Stock buttons are disabled when the stock item is deleted=1.

</details>
<details>
<summary><h3>0.3.1-beta</h3></summary>
<h4>Beta release 0.3.1, Script updates, swipe card login.</h4>

- Transaction include page styling corrected under pagination form
- Swipe card login now working. Testing pending once card reader is obtained.
- Card login page is now complete and working. Test buttons in place for passes until pass reader in place.
- Users with no theme saved can now login. Fixed the SQL query to make a LEFT JOIN for theme.
- DB install extras updated in db_extras.sql.
- Fulldump run and saved.
- Adjustments made to various pages based on installation bash script.
- Edit images button added back in to the stock edit page.
- Login page is now working for the card reader, still needs a full test but now doesn’t try to login when pressing any button.
- MySQL scripts updated to add the needed info to the DB.
- Bash script updated with some more prompts and fixed the first prompt with a case instead of else if.
- Bash script now checks whether the base_url is correct and has some delay added in for the scripts to run.
- Admin global settings is now a more cleaned up table.
- Transactions now support cable_transaction table.
- Transaction include page now supports cable_transaction page.
- Updated cable_transaction table to now include the shelf_id. SQL queries updated.
- Added error checking from URLs to the pages where they are needed and adjusted the error query strings to be more useful.
- Admin global settings restore defaults now restores the default theme too.
- Fixed some of the forms not working due to some mobile CSS format things. There might be some more to find yet.
- Corrected the README with correct PHP modules to match the install bash script
- Fixed the install bash script to install the correct modules based on testing. Now installs correctly.
- Added the start of an update script. This will be perfected in the next minor patch ready for the final release in 0.4.0-beta

</details>
<details>
<summary><h3>0.3.0-beta</h3></summary>
<h4>Beta release 0.3.0, Adjustments for mobile width and card reader tech.</h4>

- Mobile CSS in progress
- Some HTML elements are hidden/shown based on width.
- Admin page is not visible from mobile form factor unless the URL is appended.
- New CSS added for mobile form factor.
- Nav now loads properly on mobile.
- Footer now loads differently on mobile.
- Index page now works on mobile. Less columns show to reduce clutter
- Cablestock page now works on mobile.
- Stock (view) page now works on mobile.
- Stock (add) page now works on mobile.
- Stock (remove) page now works on mobile.
- Stock (move) page now works on mobile.
- Stock (edit) page now works on mobile.
- Transactions inc now working on mobile, with page numbers becoming a select field.
- Index page pagination row is now longer being sorted with the rest of the table.
- Swipe card prompt now shows up on mobile form factor.
- Swipe card fields added to users table.
- Swipe cards can now be added on the profile page.
- Swipe cards can be re-assigned on the profile page.
- login-card.inc.php added to handle card logins.
- Swipe card assigning and re-assigning is handled in admin.inc.php.
- Swipe card de-assigning is handled in admin.inc.php.
- Bootstrap 4.5.2 CSS added in assets/css folder for redundancy.
- Email example added to Email Notification Settings section of admin page via AJAX.
- Some modification to the smtp.inc.php email template to allow it to be embedded in pup page.

</details>
<details>
<summary><h3>0.2.1-beta</h3></summary>
<h4>Beta release 0.2.1, based on initial feedback.</h4>

- Added more themes. Theme CSS now has more properties which can be adjusted.
- Changelog page has been formatted better and now fills the page.
- Email notifications can now be disabled from the admin page. Each notification type is configurable, except them important things, like password resets.
- SMTP send_mail function has been updated to check if the notification type is enabled. All send_mail requests have been updated accordingly.
- Default theme selection now added to Global Settings of admin page
- Themes are now in their own table in the database and theme selection is now auto generated on pages.
- Theme test page added ad theme-test.php. This shows all relevant pages which are affected by the theme. The css can be edited, applied, downloaded and uploaded from here for new themes.
- About page added, accessible from the footer copyright.
- The http-headers.php is now merged into head.php. These were both being called at the same time so seemed pointless being split.
- Name and branding changes to StockBase. This might not be the final name.
- Footer can now be disabled from the $showFoot variable in foot.php - this will likely hidden on final release.
- Emails now have useful content in them. It used to be just numbers but now it gives relevant info.
- MySQL dumps updated.

</details>
<details>
<summary><h3>0.2.0-beta</h3></summary>
<h4>Beta release 0.2.0, based on initial feedback.</h4>

- Corrected the URL redirects when a user tries to reach a page without being logged in. Logging in now redirects to the correct page.
- Removed the title and welcome message from the index page and cablestock pages.
- Moved the title into the Nav bar and linked it to the index page.
- Corrected the issue with the offset being negative when no items are found on the index SQL query. Negative numbers now default to 0.
- Back button removed from the nav. This was creating loops where you couldn’t actually go back.
- Changed the icon in the clear button to be the fa-ban icon and rotated it 90degrees
- Changed the serial number so it can now be copied but this may be going later down the line
- Stock page now allows you to edit individual rows in the item table. This allows the adding of new serial numbers which were missed.
- Images can be permanently deleted from the admin page
- Cable stock now relates to shelves rather than just sites. This is now added correctly too.
- Stock page now hides irrelevant info for cables.
- Less important info is now under the "more info" section on the stock page.
- The "show 0 stock" button now ONLY shows 0 stock rows, now all rows.
- Corrected the cablestock searching and formatting.
- Dynamic searching is now in and working. Ajax based searching which updates on input.
- Can now search with more criteria on the home page.
- Images are now larger on the home page. This is copied throughout.
- Cablestock page now allows you to go to the stock properties page by clicking the cable name. This is the same as normal stock items, with less important info removed. 
- Can now change the image for cablestock with the above change.
- Label and Manufacturer are now select boxes rather than input because this makes more sense.
- Added deleted field to tables (item, stock, shelf, area etc) so that things can be tracked.
- Deleting stock when the stock count is 0, no longer deletes the row from the database and instead marks deleted as 1.
- Minimum stock count now checks against the site using the shelf of the object that the stock was removed from for the email notifications.
- Added a light theme (for those who no longer want their eyes), which can be enabled under the user profile section.
- Added more themes. Theme CSS now has more properties which can be adjusted.
- Email notification settings section added to admin page. This is a work in progress.
- Changelog section added to admin page. Moved from the hidden link and now shows 10 by default, with a link to the full page

</details>
<details>
<summary><h3>0.1.0-beta</h3></summary>
<h4>First beta test release of the system to be tested for install and running functionality</h4>

- Fully functioning changelog reachable from the secret admin menu - this will have a home eventually.
- Fully functional LDAP login system with failover host integration.
- Fully functional SMTP mail sending. All information is saved and pulled from the DB when requested.
- Fully customisable global settings, including logo, system name and banner colour.
- Email password resetting now possible and working.
- Bash install script implemented and functional. May need adapting as changes happen to the system.
- Email template created but could probably use some work.
- Stock editing, adding, removing and moving all functional to limited testing. Further testing needed.

<h5>Issues</h5>

- Some page redirects are not redirecting correctly and will be addressed when found.

</details>
</details>