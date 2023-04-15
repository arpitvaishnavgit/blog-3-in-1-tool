<?php

/*
  Plugin Name: Blog 3 in 1 tool
  Description: This tool assists users in determining the word and character count of a blog, as well as estimating the amount of time required to read the blog. Providing this information, it enhances the user's experience on the website.
  Version: 1.0
  Author: DBTECH
  Author URI: https://developerbazaar.com/
*/





class WordCountAndTimePlugin {

  //all hooks construct come here//
  function __construct() {
    add_action('admin_menu', array($this, 'adminPage'));
    add_action('admin_init', array($this, 'settings'));
    add_action('admin_menu', array($this, 'settings'));
    add_filter('the_content', array($this, 'ifwrap'));
    add_action('wp_enqueue_scripts', array($this, 'prefix_add_my_stylesheet'));
  }


  //style sheet register/include here//
  function prefix_add_my_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'prefix-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}


   


  //start function for displaying content to user in blog page//
  function ifwrap($content){  

    //start calling all database parameter
     $wcp_location=get_option('wcp_location');
     $wcp_input=get_option('wcp_input');
     $wcp_readtime=get_option('wcp_readtime');
     $wcp_charcount=get_option('wcp_charcount');
     $wcp_wordcount=get_option('wcp_wordcount');
    //end calling all database parameter

    //blog page content code show here// 
     $html = "<div class='box-setting'>".$wcp_input."<br>";
     $readtime_calc=round(str_word_count(strip_tags($content))/200);
     
     //conditon for checking display to user read time in minuter or second//
     if($readtime_calc=='0')
     {$readtime_calc = "Few seconds(s)";}
     else
     {$readtime_calc= $readtime_calc."minute(s)";}
     

     //how user will be see their content 
     $wordCount = "This post has ".str_word_count(strip_tags($content))." Words <br>";
     $charcount = 'This post has ' . strlen(strip_tags($content)) . ' characters.<br>';
     $readtime = 'This post will take about ' . $readtime_calc. '  to read.<br></div>';

     //conditon for checking user want to show and not show word/character/readtime count//
     if(($wcp_wordcount=='1') and (!empty($wcp_wordcount))){ $html.= $wordCount;}
     if($wcp_charcount=='1' and !empty($wcp_charcount)){ $html.=$charcount;}
     if($wcp_readtime=='1' and !empty($wcp_readtime)){ $html.=$readtime;}


    //start condition for displaying feautre to showing content top and bottom//
     if(is_single() and is_main_query() ){
        if($wcp_location==1 and !empty($wcp_location)){
          echo $content.$html;
        }
        elseif($wcp_location==0 and empty($wcp_location)){
          echo $html.$content;
        }
        else{
         echo $content;
        }
     }
    //end condition for displaying feautre to showing content top and bottom//
  }
  //end function for displaying content to user in blog page//



  //start register setting /insert data//
  function settings() {

    //setting section//
    add_settings_section('wcp_first_section', null, null, 'word-count-setting-page');


    //where display input//
    add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'word-count-setting-page', 'wcp_first_section');
    register_setting('wordcountplugin', 'wcp_location', array('sanitize_callback' => array($this, 'sanitizelocation'), 'default' => '0'));
    
    //which input show //
    add_settings_field('wcp_input', 'Display Input', array($this, 'display_input'),'word-count-setting-page','wcp_first_section');
    register_setting('wordcountplugin','wcp_input', array('sanitize_callback' => array($this,'sanitizeinput'), 'default' => 'Put your info'));
    
    //checkbox for word count  //
    add_settings_field('wcp_wordcount', 'Word Count', array($this, 'display_count'),'word-count-setting-page','wcp_first_section');
    register_setting('wordcountplugin','wcp_wordcount', array('sanitize_callback' => array($this,'sanitizeword'), 'default' => '0'));
  
    //checkbox for character count//
    add_settings_field('wcp_charcount', 'Charater Count', array($this, 'display_character'),'word-count-setting-page','wcp_first_section');
    register_setting('wordcountplugin','wcp_charcount', array('sanitize_callback' => array($this,'sanitizecharcounter'), 'default' => '0'));

    //checkbox for read time//
    add_settings_field('wcp_readtime', 'Read Time', array($this, 'read_character'),'word-count-setting-page','wcp_first_section');
    register_setting('wordcountplugin','wcp_readtime', array('sanitize_callback' =>  array($this,'sanitizereadtime'), 'default' => '0'));

    //css editor field//
    add_settings_field('wcp_css', 'CSS Editor', array($this, 'css_editor'),'word-count-setting-page','wcp_first_section');
    register_setting('wordcountplugin','wcp_css', array('sanitize_callback' =>  'sanitize_text_field', 'default' => ''));

  }
  //end register setting /insert data//



  //sanitize function start here//

  //location//
  function sanitizelocation($input){
    if($input!=0 and $input!=1){
      add_settings_error('wcp_location', 'wcp_location_error', 'Something is going wrong in your value data.');
      return get_option('wcp_location');
      }
       return $input;
    }

  //input character check//  
   function sanitizeinput($input){
    $input_content = strlen(strip_tags($input));
    if($input_content>30){
      add_settings_error('wcp_input', 'wcp_input_error', 'You can enter only 30 Character, You are using '."$input_content".' character ');
      return get_option('wcp_input');
    }
    return $input;
    }

  //wordcount//
  function sanitizeword($input){
    if($input!=1 and $input!=0){
      add_settings_error('wcp_wordcount', 'wcp_wordcount_error', 'Something is going wrong in your value data.');
      return get_option('wcp_wordcount');
    }
    return $input;
    }

  //charactercount//
  function sanitizecharcounter($input){
    if($input!=1 and $input!=0){
      add_settings_error('wcp_charcount', 'wcp_charcount_error', 'Something is going wrong in your value data.');
      return get_option('wcp_charcount');
    }
    return $input;
    }

  //readtime//
  function sanitizereadtime($input){
    if($input!=1 and $input!=0){
      add_settings_error('wcp_readtime', 'wcp_readtime_error', 'Something is going wrong in your value data.');
      return get_option('wcp_readtime');
    }
    return $input;
  }
   //sanitize function end here//




  //admin input show functions start//


  //location input//
  function locationHTML() { ?>
    <select name="wcp_location">
      <option value="0" <?php selected(get_option('wcp_location'), '0'); ?>>Beginning of post</option>
      <option value="1" <?php selected(get_option('wcp_location'), '1');?>>End of post</option>
    </select>
  <?php }

  //display input//
  function  display_input(){ ?>
    <input type="text" name="wcp_input" value="<?php echo get_option('wcp_input'); ?>">
  <?php }

  //display count//
  function display_count(){ ?>
    <input type="checkbox" name="wcp_wordcount" value="1" <?php checked(get_option('wcp_wordcount') ,1); ?> >
   <?php }

  //display character//
  function display_character(){ ?>
    <input type="checkbox" name="wcp_charcount" value="1" <?php checked(get_option('wcp_charcount') ,1); ?> > 
  <?php }

   //display reading time//
  function read_character(){ ?>
    <input type="checkbox" name="wcp_readtime" value="1" <?php checked(get_option('wcp_readtime') ,1); ?> >
  <?php }

  //display css code//
  function css_editor(){ 
    $dir = plugin_dir_path( __FILE__ );
    $css = file_get_contents(''.$dir.''.'\style.css');
    $wcp_css_data =   get_option('wcp_css');
    $myfile = fopen("$dir/style.css", "w") or die("Unable to open file!");
    $txt = "$css .body{border:green} .text{color:red}";
    fwrite($myfile, "\n". $wcp_css_data);
    fclose($myfile);
     $file_css = file_get_contents(''.$dir.''.'\style.css');
  ?>

  <textarea  name ="wcp_css" rows="10" cols ="50">
  <?php if(empty($wcp_css_data)){ echo ".box-setting { background-color: #f1f0f0; color: black; padding: 20px; margin-top: 10px; }"; }
  else{ echo $wcp_css_data;}?>
  </textarea>
  <?php }

  //admin input show functions end//



  //function to add option in wp//
  function adminPage() {
    add_options_page('Blog 3 in 1 tool Settings', 'Blog 3 in 1 tool', 'manage_options', 'word-count-setting-page', array($this, 'ourHTML'));
  }

 
  //function for start showing form and input filed here //
  function ourHTML() { ?>
    <div class="wrap">
      <h1>Word Count Settings </h1>
      <form action="options.php" method="POST">

      <?php
        settings_fields('wordcountplugin');
        do_settings_sections('word-count-setting-page');
        submit_button();
      ?>
      </form>
    </div>
  <?php }
    //function for end showing form and input filed here //

}


$wordCountAndTimePlugin = new WordCountAndTimePlugin();



