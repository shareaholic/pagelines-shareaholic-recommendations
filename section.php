<?php
/*
  Section: Shareaholic Related Content
  Author: Shareaholic
  Author URI: http://shareaholic.com
  Version: 1.0.1
  Description: Increase traffic and time on site for your content with Shareaholic’s related content tool, which recommends your own posts at the bottom of each article you publish. Choose between thumbnail and a list of links designs. Shareaholic is trusted by hundreds of thousands of websites and touches over 300 million people each month.
  Class Name: ShrRecommendationsSection
  Cloning: true
  Demo: http://www.shareaholic.com/publishers/recommendations/
  Workswith: main
 */

/*
 * PageLines Headers API
 * 
 *  Sections support standard WP file headers (http://codex.wordpress.org/File_Header) with these additions:
 *  -----------------------------------
 * 	 - Section: The name of your section.
 * 	 - Class Name: Name of the section class goes here, has to match the class extending PageLinesSection.
 * 	 - Cloning: (bool) Enable cloning features.
 * 	 - Depends: If your section needs another section loaded first set its classname here.
 * 	 - Workswith: Comma seperated list of template areas the section is allowed in.
 * 	 - Failswith: Comma seperated list of template areas the section is NOT allowed in.
 * 	 - Demo: Use this to point to a demo for this product.
 * 	 - External: Use this to point to an external overview of the product
 * 	 - Long: Add a full description, used on the actual store page on http://www.pagelines.com/store/
 *
 */

/**
 *
 * Section Class Setup
 * 
 * Name your section class whatever you want, just make sure it matches the 
 * "Class Name" in the section meta (above)
 * 
 * File Naming Conventions
 * -------------------------------------
 *  section.php 		  - The primary php loader for the section.
 *  style.css 			  - Basic CSS styles contains all structural information, no color (autoloaded)
 *  images/				    - Image folder. 
 *  thumb.png			    - Primary branding graphic (300px by 225px - autoloaded)
 *  screenshot.png		- Primary Screenshot (300px by 225px)
 *  screenshot-1.png 	- Additional screenshots: (screenshot-1.png -2 -3 etc...optional).
 *  icon.png			    - Portable icon format (16px by 16px)
 * 	color.less			  - Computed color control file (autoloaded)
 *
 */
 
class ShrRecommendationsSection extends PageLinesSection {

  function section_styles() {
    if ((isset($_GET['sb_debug']) || isset($_POST['sb_debug']))) {
      $script = 'http://www.spreadaholic.com/media/js/jquery.shareaholic-publishers-rd.js';
    }
    else
      $script = 'https://dtym7iokkjlif.cloudfront.net/media/js/jquery.shareaholic-publishers-rd.min.js';
    
    wp_enqueue_script('shareaholic-recommendations-js', $script);
  }

  function section_head() {
    if (!ploption('shr-recomm-style',$this->oset))
      $style = 'image';
    else
      $style = ploption('shr-recomm-style',$this->oset);

    if (!ploption('shr-recomm-no',$this->oset))
      $number_recomm = '3';
    else
      $number_recomm = ploption('shr-recomm-no',$this->oset);
    
    $params = array(
        'link' => get_permalink(get_the_ID()),
        'apikey' => 'ec480e025f2f0e86f41ebad0b2940ff1b',
        'number' => $number_recomm,
        'style' => $style
    );
    
    $shrsb_rd_js_params['shr_rd-' . get_the_ID()] = array_filter($params);
    $js = 'var SHRRD_Settings = ' . json_encode($shrsb_rd_js_params);
    
    echo '<script type="text/javascript">';
    echo $js;
    echo ';</script>';
  }

  function section_template() {
    $post_id =get_the_ID();
    if(!empty($post_id))
      $class="shr_rd-$post_id";
    else
      $class="shr_rd";
    ?>
    <div class="<?php echo $class?>"></div>
    <?php
  }

  function section_optionator($settings) {

    $settings = wp_parse_args($settings, $this->optionator_default);
    
    $options = array(
        'shr-recomm-style' => array(
            'type' => 'select',
            'inputlabel' => 'Select Style: Choose between a list of links to your posts or thumbnails',
            'selectvalues' => array(
                'image' => array('name' => 'Thumbnails'),
                'text'  => array('name' => 'Text')
            ),
        ),
        'shr-recomm-no' => array(
            'type' => 'select',
            'inputlabel' => 'Select Style: Select number of related posts to display',
            'selectvalues' => array(
                '3' => array('name' => '3'),
                '4' => array('name' => '4')
            ),
        ),
        'shr-recomm-terms' => array(
            'type' => 'text_content',
            'inputlabel' => 'By activating Shareaholic you agree to our <a href="http://www.shareaholic.com/terms/" target="_blank">Terms of Service</a> and <a href="http://www.shareaholic.com/privacy/" target="_blank">Privacy Policy</a>.',
            'title' => '',
            'shortexp' => '',
            'exp' => '',
        )
    );
    
    $tab_settings = array(
        'id' => 'shr-recomm-options',
        'name' => 'Shareaholic Related Content Settings',
        'icon' => $this->icon,
        'clone_id' => $settings['clone_id'],
        'active' => $settings['active']
    );
    register_metatab($tab_settings, $options, $this->class_name);
  }
}