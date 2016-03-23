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

# TODO add/update xprofile fields # https://github.com/boonebgorges/wp-cli-buddypress
# TODO migrate xprofile data

activity_id=$($wp menu item list inside-header-navigation | grep Activity | cut -f1)
[[ -n "$activity_id" ]] && $wp menu item delete $activity_id

$wp plugin deactivate --network cac-advanced-profiles

$wp plugin activate --network buddypress-global-search
$wp plugin activate --network buddypress-profile-progression
$wp plugin activate --network bp-block-member
$wp plugin activate --network buddypress-followers
$wp plugin activate --network mla-academic-interests
$wp plugin activate --network bp-blog-avatar

npm install
gulp

$wp theme activate levitin

$wp widget delete text-13 links-2 rss-5

# theme must be active for sidebars to have been registered
# all text widgets for now due to open bug adding rss widgets: https://github.com/wp-cli/wp-cli/issues/1222
$wp widget add text mla-dashboard-top --title='What is MLA Commons?'

$wp widget add text mla-dashboard-left --title='MLA Blogs'
$wp widget add text mla-dashboard-left --title='MLA News'
$wp widget add text mla-dashboard-left --title='Featured MLA Publication'
$wp widget add text mla-dashboard-left --title='Advocacy Site'

$wp widget add text mla-dashboard-right --title='Commons Member Twitter Feed' --text='Enim corporis est et tempore numquam. Id id quia iste in voluptatibus porro cupiditate. Et alias in est rerum officiis fuga. Veniam officia quia et error sed sed est ipsum. Animi veritatis inventore saepe in hic sunt.
Sint consequatur qui corporis non impedit recusandae quasi explicabo. Sint natus est sapiente. Quia aut repudiandae modi error iusto perspiciatis aut.
Et impedit nisi nostrum. Dolorum voluptas unde odio quia ipsum. Architecto et nam dolorem repellendus omnis nulla. Vitae earum quisquam dolorum. Totam iusto mollitia ut. Quidem qui velit odit numquam.
Inventore culpa tempore expedita omnis est vel quis aut. Et voluptate perspiciatis eaque architecto qui in. Et soluta et et. Et autem consectetur nihil rerum provident. Porro accusamus quia distinctio. Quis vitae facere ut porro eum ex minus occaecati.'
$wp widget add text mla-dashboard-right --title='Featured Member Site'
$wp widget add text mla-dashboard-right --title='Featured CORE Collection'

$wp menu location assign inside-header-navigation primary_navigation # theme must be activated in order for wordpress to find primary_navigation

# doesn't hurt anything to leave this, and it comes in handy for ad-hoc uses of wp-cli
#rm $pre_php
