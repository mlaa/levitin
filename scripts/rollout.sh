#!/bin/bash
set -ex

# tmp file to disable extraneous output from php when running wp-cli
# http://wordpress.stackexchange.com/a/145313/66711
pre_php=/tmp/__pre.php
echo "<?php error_reporting( 0 ); define( 'WP_DEBUG', false );" > "$pre_php"

wp="wp --path=/srv/www/commons/current/web/wp --require=$pre_php"
theme_dir=/srv/www/commons/current/web/app/themes/levitin

if [[ "$PWD" != "$theme_dir" ]]
then
	echo "This script must be run from '$theme_dir'."
	exit 1
fi

widgets() {
	for i in deposits-directory-sidebar deposits-search-sidebar wp_inactive_widgets
	do
		$wp widget list $i
	done
	return

	#$wp widget list

	# Temporarily move widgets to "wp_inactive_widgets" area so we can save their content while we change the theme.
	# Dashboard Widgets: 1. rss-5: "News from the MLA"; 2. text-15: "MLA Sites"; 3. links-2: "Member Resources"
	# 4. rss-6: "New from the MLA" (copy); 5. links-3: "Member Resources" (copy)
	# Footer Widgets: 1. text-6: "Contact Us"; 3. text-10: "Get Help"
	for widget in rss-5 text-15 links-2 text-6 text-10 rss-6 links-3
	do
		$wp widget move $widget --sidebar-id=wp_inactive_widgets
	done

	$wp widget delete text-13 # Delete "More Resources" widget
	$wp widget delete rss-3 # Delete "FAQ" widget (add link to "Get Help" widget instead)

	$wp widget add mla_bp_profile_area sidebar-primary # Add profile area to dashboard sidebar

	$wp widget move rss-5 --sidebar-id=mla-dashboard-main # Add "News from the MLA" to logged-out dashboard main area.
	$wp widget move links-2 --sidebar-id=mla-dashboard-logged-out # Add "MLA Resources" to the logged-out sidebar.

	# Populate footer with footer widgets
	for widget in text-6 rss-3 text-10
	do
		$wp widget move $widget --sidebar-id=sidebar-footer
	done

	# Move "News from the MLA" (copy), "MLA Sites," and "Member Resources" to the tabbed sidebar,
	# visible to logged-in users only.
	for widget in links-3 text-15 rss-6
	do
		$wp widget move $widget --sidebar-id=mla-dashboard-tabbed-sidebar
	done
}

npm install
gulp

# TODO add/update xprofile fields
# TODO migrate xprofile data

#widgets
#exit

activity_id=$($wp menu item list inside-header-navigation | grep Activity | cut -f1)
[[ -n "$activity_id" ]] && $wp menu item delete $activity_id

$wp plugin deactivate --network cac-advanced-profiles

$wp plugin activate --network buddypress-global-search
$wp plugin activate --network buddypress-profile-progression
$wp plugin activate --network bp-block-member
$wp plugin activate --network buddypress-followers
$wp plugin activate --network mla-academic-interests

$wp theme activate levitin

$wp menu location assign inside-header-navigation primary_navigation # theme must be activated in order for wordpress to find primary_navigation

rm $pre_php
