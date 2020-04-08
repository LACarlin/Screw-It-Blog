<?php

class Blog {

    public $title;
    public $body;
    public $body2;
    public $date_posted;
    // public $tag;
    //private $main_image;
    //private $second_image;
    //private $third_image;
    public $category;

    //private $facebook_url;
    //private $insta_url;
    //private $twitter_url;

    function __construct($title, $body, $body2, $date_posted, $category) {

        $this->title = $title;
        $this->body = $body;
        $this->body2 = $body2;
        $this->date_posted = $date_posted;
        // $this->main_image = $main_image;
        //$this->second_image = $second_image;
        //$this->third_image = $third_image;
        $this->category = $category;
        //$this->tag= $tag;
        //$this->facebook_url = $facebook_url;
        //$this->insta_url = $insta_url;
        //$this->twitter_url= $twitter_url;
    }


    public static function allCreate() {
        $list = [];
        $db = Screw_it::getInstance();
        $req = $db->query("SELECT * FROM blog_posts WHERE category = 'create'; 
                          "); //order by most recent *ASK MARTINA*
        // we create a list of blog_post objects from the database results
        foreach ($req->fetch(PDO::FETCH_ASSOC) as $blog) {
            $list[] = new Blog($blog['user_id'], $blog['title'], $blog['body'], $blog['body2'], $blog['date_posted'], $blog['main_image']);
        }
        return $list;
    }

    public static function find($blog_id) {

        $db = Screw_it::getInstance();
        $blog_id = intval($blog_id);
        $req = $db->prepare('SELECT * FROM blog_posts WHERE blog_id = :blog_id;');
        
        
        if (!$req){
            echo "error, pls handle";
        } 
        
        $req->execute(array('blog_id' => $blog_id));
        $blog = $req->fetch();
        
        return $blog;
        
        /*
        $req = $db->prepare("SELECT * FROM blog_post WHERE blog_id = :blog_id;
                           ");
        //query has been prepared replace :blog_id with actual value 
        $req->execute(array('blog_id' => $blog_id));
        $blog = $req->fetch();

        if ($blog) {
            return new Blog($blog['user_id'], $blog['title'], $blog['body'], $blog['body2'], $blog['date_posted'], $blog['main_image']);
        } else {
            throw new Exception('Blog not found, please search again');
        }
    }*/
    }
    
    public static function add() {

        $db = Screw_it::getInstance();

        if (isset($_POST['submit'])) {

// set parameters and execute
            if (isset($_POST['title']) && $_POST['title'] != "") {
                $filteredTitle = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
            }
            if (isset($_POST['body']) && $_POST['body'] != "") {
                $filteredBody = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_SPECIAL_CHARS);
            }
            if (isset($_POST['body2']) && $_POST['body2'] != "") {
                $filteredBody2 = filter_input(INPUT_POST, 'body2', FILTER_SANITIZE_SPECIAL_CHARS);
            }
            if (isset($_POST['category']) && $_POST['category'] != "") {
                $filteredCategory = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
            }

            if (isset($_POST['tag']) && $_POST['tag'] != "") {
                $filteredTag = filter_input(INPUT_POST, 'tag', FILTER_SANITIZE_STRING);
            }


            /* if (isset($_POST['myfile']) && $_POST['myfile'] != "") {
              $filteredImage = filter_input(INPUT_POST, 'myfile', FILTER_SANITIZE_SPECIAL_CHARS);
              } */
            $filteredImage = filter_input(INPUT_POST, 'myfile', FILTER_SANITIZE_SPECIAL_CHARS);
            $filteredImage = $_FILES['myfile']['name'];
            $location = "views/images/";
            $file_path = $location . $filteredImage;

            //for user_id once sessions is done it will need to be the session(user_id) that would go into the values for user_id!!
            $req = $db->prepare("INSERT INTO blog_posts(user_id, title, body, body2, main_image, category) VALUES ('9', :title, :body, :body2, :imagename, :category);
                INSERT INTO tags (tag_id) VALUES (:tag);
                ");

            /* $req=$db->prepare('insert into images (image) VALUES (:path);');
              $req->bindParam(':path', $image);
              $image = $destinationFile;

              $req->execute(); */


            $req->bindParam(':title', $title);
            $req->bindParam(':body', $body);
            $req->bindParam(':body2', $body2);
            $req->bindParam(':category', $category);
            $req->bindParam(':imagename', $image);
            $req->bindParam(':tag', $tag);

            $title = $filteredTitle;
            $body = $filteredBody;
            $body2 = $filteredBody2;
            $category = $filteredCategory;
            $image = $file_path;
            $tag = $filteredTag;

            $req->execute();

//upload product image:  
            Blog::uploadFiles($image);
        }
    }

    const AllowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    const InputKey = 'myfile';

//die() function calls replaced with trigger_error() calls
//replace with structured exception handling
    public static function uploadFiles($image) {

        $db = Screw_it::getInstance();

        /* $file_uploaded = isset($_FILES['myfile']['name']) && $_FILES['myfile']['name'] !== '';
          $file = $_FILES['myfile'];
          $file_name = $file['name'];
          echo($file_name); */

        $image = $_FILES[self::InputKey]['name'];

        //$image = pathinfo($_FILES['myfile']['name'], PATHINFO_FILENAME);


        if (empty($_FILES[self::InputKey])) {
            //die("File Missing!");
            trigger_error("File Missing!");
        }

        if ($_FILES[self::InputKey]['error'] > 0) {
            trigger_error("Handle the error! " . $_FILES[self::InputKey]['error']);
        }

        if (!in_array($_FILES[self::InputKey]['type'], self::AllowedTypes)) {
            trigger_error("Handle File Type Not Allowed: " . $_FILES[self::InputKey]['type']);
        }

        $tempFile = $_FILES[self::InputKey]['tmp_name'];
        $path = "/Applications/XAMPP/xamppfiles/htdocs/Screw-it/views/images/";
        $destinationFile = $path . $image;

        (move_uploaded_file($_FILES[self::InputKey]['tmp_name'], $destinationFile));


        /* if (!move_uploaded_file($_FILES[self::InputKey]['tmp_name'], $destinationFile)) { //file does upload not usre why throwing error?
          trigger_error("File not uploaded");
          } else {
          echo "you have uploaded successfully!";
          } */

        //Clean up the temp file
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    public static function remove($blog_id) {
        $db = Screw_it::getInstance();
        //make sure $id is an integer
        $blog_id = intval($blog_id);
        $req = $db->prepare('delete FROM blog_posts WHERE blog_id = :blog_id');
        // the query was prepared, now replace :id with the actual $id value
        $req->execute(array('blog_id' => $blog_id));
    }

}
