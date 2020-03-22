<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines http://www.simplemachines.org
 * @copyright 2019 Simple Machines and individual contributors
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.1 RC2
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	https://www.simplemachines.org/
*/

/**
 * Initialize the template... mainly little settings.
 */
function template_init()
{
	global $settings, $txt;

	/* $context, $options and $txt may be available for use, but may not be fully populated yet. */

	// The version this template/theme is for. This should probably be the version of SMF it was created for.
	$settings['theme_version'] = '2.1';

	// Use plain buttons - as opposed to text buttons?
	$settings['use_buttons'] = true;

	// Set the following variable to true if this theme requires the optional theme strings file to be loaded.
	$settings['require_theme_strings'] = true;

	// Set the following variable to true if this theme wants to display the avatar of the user that posted the last and the first post on the message index and recent pages.
	$settings['avatars_on_indexes'] = false;

	// Set the following variable to true if this theme wants to display the avatar of the user that posted the last post on the board index.
	$settings['avatars_on_boardIndex'] = false;

	// This defines the formatting for the page indexes used throughout the forum.
	$settings['page_index'] = array(
		'extra_before' => '<span class="page-item disabled"> <span class="pages page-link">' . $txt['pages'] . '</span></span>',
		'previous_page' => 		'<span class="material-icons">keyboard_arrow_left</span>',
		'current_page' => '<span class="page-item active"><a class="page-link current_page" href="#" tabindex="-1" aria-disabled="true">%1$d</a></span>',
		'page' => ' <span class="page-item"><a class="page-link" href="{URL}">%2$s</a></span>',
		'expand_pages' => '<span class="page-link expand_pages" onclick="expandPages(this, {LINK}, {FIRST_PAGE}, {LAST_PAGE}, {PER_PAGE});"> ... </span>',
		'next_page' =>	'<span class="material-icons">keyboard_arrow_right</span>',
		'extra_after' => '',
	);


	// Allow css/js files to be disabled for this specific theme.
	// Add the identifier as an array key. IE array('smf_script'); Some external files might not add identifiers, on those cases SMF uses its filename as reference.
	if (!isset($settings['disable_files']))
		$settings['disable_files'] = array();
}

/**
 * The main sub template above the content.
 */
function template_html_above()
{
	global $context, $scripturl, $txt, $modSettings;

	// Show right to left, the language code, and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', !empty($txt['lang_locale']) ? ' lang="' . str_replace("_", "-", substr($txt['lang_locale'], 0, strcspn($txt['lang_locale'], "."))) . '"' : '', '>
<head>
	<meta charset="', $context['character_set'], '">';

	/*
		You don't need to manually load index.css, this will be set up for you.
		Note that RTL will also be loaded for you.

		The most efficient way of writing multi themes is to use a master
		index.css plus variant.css files. If you've set them up properly
		(through $settings['theme_variants']), the variant files will be loaded
		for you automatically.

		If you want to load other CSS files, the best way is to use the
		'integrate_load_theme' integration hook and the loadCSSFile() function.
		This approach will let you take advantage of SMF's automatic CSS
		minimization and other benefits. You can, of course, manually add any
		other files you want after template_css() has been run.
	*/

	// load in any css from mods or themes so they can overwrite if wanted

	//loadCSSFile('theme.css', array('force_current' => false,'validate' => true, 'order_pos' => -500), 'spirate_theme');

	template_css();
	//var_dump(loadCSSFile('theme.css', array('force_current' => false,'validate' => true, 'order_pos' => 1), 'Spirate_theme'));
	//loadJavaScriptFile('index.js', array('defer' => false,'minimize' => false), 'theme_plugins');
	//loadJavaScriptFile('theme.js', array('defer' => false,'minimize' => false), 'theme_plugins');
	// load in any javascript files from mods and themes
	template_javascript(); 
	echo '
	<title>', $context['page_title_html_safe'], '</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">';

	// Content related meta tags, like description, keywords, Open Graph stuff, etc...
	foreach ($context['meta_tags'] as $meta_tag)
	{
		echo '
	<meta';

		foreach ($meta_tag as $meta_key => $meta_value)
			echo ' ', $meta_key, '="', $meta_value, '"';

		echo '>';
	}

	/*	What is your Lollipop's color?
		Theme Authors, you can change the color here to make sure your theme's main color gets visible on tab */
	echo '
	<meta name="theme-color" content="#557EA0">';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex">';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '">';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help">
	<link rel="contents" href="', $scripturl, '">', ($context['allow_search'] ? '
	<link rel="search" href="' . $scripturl . '?action=search">' : '');

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?action=.xml;type=rss2', !empty($context['current_board']) ? ';board=' . $context['current_board'] : '', '">
	<link rel="alternate" type="application/atom+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['atom'], '" href="', $scripturl, '?action=.xml;type=atom', !empty($context['current_board']) ? ';board=' . $context['current_board'] : '', '">';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['links']['next']))
		echo '
	<link rel="next" href="', $context['links']['next'], '">';

	if (!empty($context['links']['prev']))
		echo '
	<link rel="prev" href="', $context['links']['prev'], '">';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0">';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body id="', $context['browser_body_id'], '" class="action_', !empty($context['current_action']) ? $context['current_action'] : (!empty($context['current_board']) ?
		'messageindex' : (!empty($context['current_topic']) ? 'display' : 'home')), !empty($context['current_board']) ? ' board_' . $context['current_board'] : '', '">
<div id="footerfix">';
}

/**
 * The upper part of the main template layer. This is the stuff that shows above the main forum content.
 */
function template_body_above()
{
	global $context, $settings, $scripturl, $txt, $modSettings, $maintenance;
	
		echo'
		<nav class="navbar_user navbar navbar-expand justify-content-end"> 
			<div class="container">
				';
			 
				if ($context['allow_search'])
				{
					echo '
				<div class="collapse navbar-collapse justify-content-start" id="navbarSupportedContent">
					<ul class="navbar-nav">
					 <li>
						<form class="form-inline" id="search_form"   action="', $scripturl, '?action=search2" method="post" accept-charset="', $context['character_set'], '">
 						<input type="search" name="search" class="form-control mr-sm-2" id="inlineFormInputName2" placeholder="', $txt['search'], '">

		
						<div class="form-check mr-sm-2">';

						// Using the quick search dropdown?
						$selected = !empty($context['current_topic']) ? 'current_topic' : (!empty($context['current_board']) ? 'current_board' : 'all');
				
						echo '
						<select  class="custom-select my-1 mr-sm-2" name="search_selection">
							<option value="all"', ($selected == 'all' ? ' selected' : ''), '>', $txt['search_entireforum'], ' </option>';
		
				// Can't limit it to a specific topic if we are not in one
				if (!empty($context['current_topic']))
					echo '
							<option value="topic"', ($selected == 'current_topic' ? ' selected' : ''), '>', $txt['search_thistopic'], '</option>';
		
				// Can't limit it to a specific board if we are not in one
				if (!empty($context['current_board']))
					echo '
							<option value="board"', ($selected == 'current_board' ? ' selected' : ''), '>', $txt['search_thisboard'], '</option>';
		
				// Can't search for members if we can't see the memberlist
				if (!empty($context['allow_memberlist']))
					echo '
							<option value="members"', ($selected == 'members' ? ' selected' : ''), '>', $txt['search_members'], ' </option>';
		
				echo '
						</select>';
						echo' 
						</div>';
				 // Search within current topic?
				if (!empty($context['current_topic']))
				echo '
					<input type="hidden" name="sd_topic" value="', $context['current_topic'], '">';

				// If we're on a certain board, limit it to this board ;).
				elseif (!empty($context['current_board']))
					echo '
						<input type="hidden" name="sd_brd" value="', $context['current_board'], '">';

						echo'

						<button type="submit" class="btn btn-primary" name="search2">', $txt['search'], '</button>
						<input type="hidden" name="advanced" value="0">
						</form> 
						</li>
				</div> ';
				}
				echo'
				
				<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
				<ul class="navbar-nav">'; 

				if (!$context['user']['is_logged'])
				template_quick_login();

				if ($context['user']['is_logged'])
		{
				// Secondly, PMs if we're doing them
			if ($context['allow_pm'])
			echo'
				<li class="nav-item dropdown">
					<a id="pm_menu_top" class="nav-link dropdown-toggle ', !empty($context['self_pm']) ? 'active' : '', '" href="', $scripturl, '?action=pm" role="button" id="pm_menu_top_content" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						', $txt['pm_short'], !empty($context['user']['unread_messages']) ? ' <span class="amt">' . $context['user']['unread_messages'] . '</span>' : '', '
					</a>
					<div id="pm_menu" class="top_menu scrollable dropdown-menu dropdown-menu-right" aria-labelledby="pm_menu_top_content" style="width: 280px;"> 
					</div>
				</li>';

				echo'		
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle ', !empty($context['self_alerts']) ? 'active' : '', ' "  href="', $scripturl, '?action=profile;area=showalerts;u=', $context['user']['id'], '"id="alerts_menu_top" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">', $txt['alerts'], !empty($context['user']['alerts']) ? ' <span class="amt">' . $context['user']['alerts'] . '</span>' : '', '</a> 
					<div id="alerts_menu" class="top_menu scrollable dropdown-menu dropdown-menu-right" aria-labelledby="alerts_menu_top" style="width: 280px;">		 
					</div>
				</li>		 
				<li class="nav-item dropdown">
					<a  href="', $scripturl, '?action=profile" id="profile_menu_top" onclick="return false;" class="', !empty($context['self_profile']) ? ' active' : '', ' nav-link dropdown-toggle" href="#" id="profile_menu_info" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					',$context['user']['name'],' ',$context['user']['avatar']['image'],'
					</a>
					<div id="profile_menu"  class="dropdown-menu top_menu dropdown-menu-right" aria-labelledby="profile_menu_info">
		
					</div>
				</li>';
			}
				echo'
				</ul> 
			</div>
			</div>
		</nav>
		';	 
	

		echo '
		<div id="header">
		<div class="container">
			<div class="row no-gutters">
			<div class="col-12 col-md-7 ">
			<h1 class="forumtitle">
				<a id="top" href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? $context['forum_name_html_safe'] : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name_html_safe'] . '">', '</a>
			</h1>
			', empty($settings['site_slogan']) ? '<img id="smflogo" src="' . $settings['images_url'] . '/smflogo.svg" alt="Simple Machines Forum" title="Simple Machines Forum">' : '<div id="siteslogan">' . $settings['site_slogan'] . '</div>', '
			</div>  
			<div class="col-12 col-md-5 ">';
			
			echo'</div>   
			';  
			
		echo '
				</div>  
			</div>  
		</div> ';
	 	// Show the menu here, according to the menu sub template, followed by the navigation tree.
	// Load mobile menu here
 	  
	 
	  template_menu();
	
	// Show the  linktree
	
	theme_linktree();
	echo'
	<div id="wrapper">';  

	// The main content should go here.
	echo '
		<div id="content_section"   class="container">
			<div id="main_content_section">';
}

/**
 * The stuff shown immediately below the main content, including the footer
 */
function template_body_below()
{
	global $context, $txt, $scripturl, $modSettings,$settings;
	echo'</div><!-- #main_content_section -->
	';
	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']) && !empty($context['random_news_line']))
		echo '
					<div class="custom-news">
						<strong>', $txt['news'], ': </strong>
						<span>', $context['random_news_line'], '</span>
					</div>';
	echo '
			
		</div><!-- #content_section -->
	</div><!-- #wrapper -->
</div><!-- #footerfix -->';

	// Show the footer with copyright, terms and help links.
	echo '
	<div id="footer">
		<div class="inner_wrap">';

	// There is now a global "Go to top" link at the right.
	echo '
		<ul>
			<li class="floatright"> <a href="', $scripturl, '?action=help">', $txt['help'], '</a> ', (!empty($modSettings['requireAgreement'])) ? '| <a href="' . $scripturl . '?action=help;sa=rules">' . $txt['terms_and_rules'] . '</a>' : '', ' | <a href="#footerfix">', $txt['go_up'], ' &#9650;</a></li>
			<li class="copyright">', theme_copyright(), '</li>
		</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p>', sprintf($txt['page_created_full'], $context['load_time'], $context['load_queries']), '</p>';

	echo '
		</div>
	</div><!-- #footer -->';

}

/**
 * This shows any deferred JavaScript and closes out the HTML
 */
function template_html_below()
{
	// Load in any javascipt that could be deferred to the end of the page
	template_javascript(true);
 
	echo '
</body>
</html>';
}

/**
 * Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
 *
 * @param bool $force_show Whether to force showing it even if settings say otherwise
 */
function theme_linktree($force_show = false)
{
	global $context, $shown_linktree, $scripturl, $txt;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;
	echo '<nav id="breadcrumb" aria-label="breadcrumb">
			<div class="container">
				<ol class="breadcrumb navigate_section">'; 

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
						<li class="breadcrumb-item ', ($link_num == count($context['linktree']) - 1) ? 'active' : '', ' ">';

		
		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'], ' ';

		// Show the link, including a URL if it should have one.
		if (isset($tree['url']))
			echo '
							<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>';
		else
			echo '
							<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo ' ', $tree['extra_after'];

		echo '
						</li>';
	}

	echo '
					</ol>
					</div>
				</nav><!-- nav .navigate_section -->';

	$shown_linktree = true;
}

/**
 * Show the menu up top. Something like [home] [help] [profile] [logout]...
 */
function template_menu()
{
	global $context;

	echo '
	<nav id="main_menu">

	<div class="container">
		<ul class="nav nav-tabs"> ';

	// Note: Menu markup has been cleaned up to remove unnecessary spans and classes.
	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
						<li class="nav-item button_', $act, ' ', !empty($button['sub_buttons']) ? 'dropdown' : '', '">
							<a class="nav-link  ', !empty($button['sub_buttons']) ? 'dropdown-toggle ' : '', ' ', $button['active_button'] ? 'active' : '', ' "  ', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '    ', !empty($button['sub_buttons']) ? 'data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"' : 'href="'. $button['href']. '"', '>
								', $button['icon'], '<span class="textmenu">', $button['title'], !empty($button['amt']) ? ' <span class="amt">' . $button['amt'] . '</span>' : '', '</span>
							</a>';

		// 2nd level menus
		if (!empty($button['sub_buttons']))
		{
			echo '
							<div class="dropdown-menu">';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				 
				echo '
								<div  class="child_', $act, '" ', !empty($childbutton['sub_buttons']) ? ' ' : '', '>
									<a class="dropdown-item" href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
										', $childbutton['title'], !empty($childbutton['amt']) ? ' <span class="amt">' . $childbutton['amt'] . '</span>' : '', '
									</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
									<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
										<li>
											<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
												', $grandchildbutton['title'], !empty($grandchildbutton['amt']) ? ' <span class="amt">' . $grandchildbutton['amt'] . '</span>' : '', '
											</a>
										</li>';

					echo '
									</ul>';
				}

				echo '
								</div>';
			}
			echo '
							</div>';
		}
		echo '
						</li>';
	}

	echo ' 
		</ul>
	  </div><!-- .container -->
	</nav><!-- .nav -->';
}

/**
 * Generate a strip of buttons.
 *
 * @param array $button_strip An array with info for displaying the strip
 * @param string $direction The direction
 * @param array $strip_options Options for the button strip
 */
function template_button_strip($button_strip, $direction = '', $strip_options = array())
{
	global $context, $txt;

	if (!is_array($strip_options))
		$strip_options = array();

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		// As of 2.1, the 'test' for each button happens while the array is being generated. The extra 'test' check here is deprecated but kept for backward compatibility (update your mods, folks!)
		if (!isset($value['test']) || !empty($context[$value['test']]))
		{
			if (!isset($value['id']))
				$value['id'] = $key;

			$button = '
				<a class="dropdown-item button_strip_' . $key . (!empty($value['active']) ? ' active' : '') . (isset($value['class']) ? ' ' . $value['class'] : '') . '" ' . (!empty($value['url']) ? 'href="' . $value['url'] . '"' : '') . ' ' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '>' . $txt[$value['text']] . '</a>';

			if (!empty($value['sub_buttons']))
			{
				$button .= '
					<div class="top_menu dropmenu ' . $key . '_dropdown">
						<div class="viewport">
							<div class="overview">';
				foreach ($value['sub_buttons'] as $element)
				{
					if (isset($element['test']) && empty($context[$element['test']]))
						continue;

					$button .= '
								<a href="' . $element['url'] . '"><strong>' . $txt[$element['text']] . '</strong>';
					if (isset($txt[$element['text'] . '_desc']))
						$button .= '<br><span>' . $txt[$element['text'] . '_desc'] . '</span>';
					$button .= '</a>';
				}
				$button .= '
							</div><!-- .overview -->
						</div><!-- .viewport -->
					</div><!-- .top_menu -->';
			}

			$buttons[] = $button;
		}
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	echo'
	<div class="dropdown ', !empty($direction) ? ' float-' . $direction : '', '" ', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"' : ''), '>
		<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			',$txt['mobile_action'],'
		</button>
		<div class="dropdown-menu ', !empty($direction) ? 'dropdown-menu-'.$direction: '', '" " aria-labelledby="dropdownMenuButton">
		', implode('', $buttons), '
		</div>
	</div>';
 
}

/**
 * The upper part of the maintenance warning box
 */
function template_maint_warning_above()
{
	global $txt, $context, $scripturl;

	echo '
	<div class="errorbox" id="errors">
		<dl>
			<dt>
				<strong id="error_serious">', $txt['forum_in_maintenance'], '</strong>
			</dt>
			<dd class="error" id="error_list">
				', sprintf($txt['maintenance_page'], $scripturl . '?action=admin;area=serversettings;' . $context['session_var'] . '=' . $context['session_id']), '
			</dd>
		</dl>
	</div>';
}

/**
 * The lower part of the maintenance warning box.
 */
function template_maint_warning_below()
{

}
function template_quick_login(){
	global $context, $settings, $scripturl, $modSettings, $txt;
	echo'
	<li>
	<div class="dropdown">
	<button class="btn btn-secondary dropdown-toggle" type="button" id="quick_login" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	', $txt['login'], '
	</button>
	<div class="dropdown-menu dropdown-menu-right" aria-labelledby="quick_login">
	<form class="px-4 py-3"  action="', $context['login_url'], '" name="frmLogin" id="frmLogin" method="post" accept-charset="', $context['character_set'], '">
		<div class="form-group">
			<label for="username">', $txt['username'], '</label>
			<input type="text"  name="user" class="form-control" id="username" placeholder="', $txt['username'], '">
		</div>
		<div class="form-group">
			<label for="password">', $txt['password'], '</label>
			<input type="password"  name="passwrd" class="form-control" id="password" placeholder="', $txt['password'], '">
		</div>
		<div class="form-group">
			<div class="form-check">
			<input type="checkbox" class="form-check-input" id="cookieneverexp">
			<label class="form-check-label" name="cookieneverexp"  for="cookieneverexp">
				',$txt['dyz_rmember'] ,'
			</label>
			</div>
		</div>
		<button type="submit" class="btn btn-primary mb-2 w-100">', $txt['login'], '</button>
 		<input type="hidden" name="hash_passwrd" value="">
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
		<input type="hidden" name="', $context['login_token_var'], '" value="', $context['login_token'], '">
		</form>
	<div class="dropdown-divider"></div>
	<a class="dropdown-item" href="', $scripturl, '?action=reminder">', $txt['forgot_your_password'], '</a>
	<a class="dropdown-item" href="', $scripturl, '?action=signup">',$txt['dyz_new'],'</a>
 	</div>
	</div>
	</li>
 
 
	';
}

?>