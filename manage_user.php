<?php 
include('db_connect.php');
session_start();

// Initialize meta array to avoid undefined variable errors
$meta = array();

if(isset($_GET['id'])){
    // Define valid table types
    $type = array("users", "employee_list", "evaluator_list");
    
    // Validate login_type to prevent invalid table access
    $login_type = isset($_SESSION['login_type']) ? intval($_SESSION['login_type']) : 0;
    if($login_type < 0 || $login_type > 2) {
        $login_type = 0; // Default to users table if invalid
    }
    
    $table = $type[$login_type];
    $user_id = intval($_GET['id']);
    
    // Query the appropriate table
    $user = $conn->query("SELECT * FROM $table WHERE id = $user_id");
    
    if($user && $user->num_rows > 0){
        $user_data = $user->fetch_assoc();
        foreach($user_data as $k => $v){
            $meta[$k] = $v;
        }
    } else {
        // If user not found, try to get from users table as fallback
        $user = $conn->query("SELECT * FROM users WHERE id = $user_id");
        if($user && $user->num_rows > 0){
            $user_data = $user->fetch_assoc();
            foreach($user_data as $k => $v){
                $meta[$k] = $v;
            }
        }
    }
}
?>
<div class="container-fluid">
    <div id="msg"></div>
    
    <form action="" id="manage-user" enctype="multipart/form-data">    
        <input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id'] : '' ?>">
        <div class="form-group">
            <label for="name">First Name</label>
            <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['firstname']) ? $meta['firstname'] : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="name">Last Name</label>
            <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['lastname']) ? $meta['lastname'] : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" class="form-control" value="<?php echo isset($meta['email']) ? $meta['email'] : '' ?>" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
            <small><i>Leave this blank if you don't want to change the password.</i></small>
        </div>
        <div class="form-group">
            <label for="" class="control-label">Avatar</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
        </div>
        <div class="form-group d-flex justify-content-center">
            <img src="<?php echo isset($meta['avatar']) && !empty($meta['avatar']) ? 'assets/uploads/'.$meta['avatar'] : 'assets/uploads/no-image-available.png' ?>" alt="User Avatar" id="cimg" class="img-fluid img-thumbnail">
        </div>
        
        <div class="form-group text-center">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>
<style>
    img#cimg{
        height: 15vh;
        width: 15vh;
        object-fit: cover;
        border-radius: 100% 100%;
    }
</style>
<script>
    function displayImg(input,_this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $('#manage-user').submit(function(e){
        e.preventDefault();
        start_load();
        
        // Basic validation
        var firstname = $('#firstname').val().trim();
        var lastname = $('#lastname').val().trim();
        var email = $('#email').val().trim();
        
        if(firstname === '' || lastname === '' || email === '') {
            alert_toast("Please fill in all required fields", 'error');
            end_load();
            return false;
        }
        
        $.ajax({
            url: 'ajax.php?action=save_user',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp){
                if(resp == 1){
                    alert_toast("Data successfully saved", 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                } else {
                    $('#msg').html('<div class="alert alert-danger">Error saving data: ' + resp + '</div>');
                    end_load();
                }
            },
            error: function(xhr, status, error) {
                $('#msg').html('<div class="alert alert-danger">AJAX Error: ' + error + '</div>');
                end_load();
            }
        });
    });
</script>