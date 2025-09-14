<?php
// Try PHP header redirect first
if(!headers_sent()) {
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        header("Location: index.php?page=new_pending_evaluation_record&id=".$id);
        exit();
    } else {
        header("Location: index.php?page=new_pending_evaluation_record");
        exit();
    }
}

// If headers already sent, use JavaScript redirect
if(isset($_GET['id'])){
    $id = $_GET['id'];
    echo '<script>window.location.href = "index.php?page=new_pending_evaluation_record&id='.$id.'";</script>';
} else {
    echo '<script>window.location.href = "index.php?page=new_pending_evaluation_record";</script>';
}
exit();
?>