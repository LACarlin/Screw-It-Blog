<?php
include_once $_SERVER ['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'Screw-it' . DIRECTORY_SEPARATOR . 'connection.php';
//error_reporting (E_ALL ^ E_NOTICE);

$db = Screw_it::getInstance();

//gets the last inserted blog_id 
$selectquery = "SELECT blog_id FROM comments ORDER BY comment_id DESC LIMIT 1";
$result = $db->query($selectquery);
$row = $result->fetch(PDO::FETCH_ASSOC);
$row['blog_id'];


$blog_id = $row['blog_id'];


//selects the comment from the db
$query = "SELECT * FROM comments 
         INNER JOIN users ON comments.user_id = users.user_id
         WHERE blog_id = '".$blog_id."'
         ORDER by comment_id desc
         ";

$stmt = $db->prepare($query);

$stmt->execute(array(
    ':blog_id' => $blog_id
));

$result = $stmt->fetchAll();

$output = '';

foreach ($result as $row) {

    $output .= '
              <div id="comm-cont"class="comment" style="margin-top: 50px; color:black;">
             <b> <div class="panel-heading username" style="font-size: 1.1em;"><span class="user-comment"> By ' . $row["username"] . ' </span></b><br> <i style="font-size:0.8em;"> on ' . $row["comment_date"] . '</i> </div>
                  <div class="comment" style="margin-bottom:10px;" > ' . $row["comment"] . '</div>           
             </div>
    ';
}
//include_once $_SERVER ['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'Screw-it' . DIRECTORY_SEPARATOR . 'controllers/blog_controller.php';
echo $output;
?>

<style>
    .btn{
        background-color: #fca15f;
        border: none;
    }

    .btn:hover{
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.12), 0 6px 20px 0 rgba(0, 0, 0, 0.05);
        background-color: #e88f4f;

    }

    .reply-btn {
        background-color: #fca15f;
        border: 20px;
        color: white;
        padding: 5px 8px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 12px;
        margin: 4px 2px;
        cursor: pointer;
    }

    .comm-cont {
        margin: auto;
        widht:50%;
    }

    @media only screen and (max-width: 400px) {

        .username-reply {
            font-size:0.7em;
        }

        .username {
            font-size:0.8em;
        }

        .comment-reply {
            font-size:0.9em;
        }

        .comment {
            font-size:0.9em;

        }

        .reply-btn {
            font-size:0.5em;
            padding: 10px 4px;
        }

        .reply-box {
            margin-bottom: 20px
        }

    }
</style>