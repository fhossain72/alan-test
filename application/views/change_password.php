<div class="row">
    <div class="col-md-12">
        <div class="user_login">
            <h2 class="montserrat"><?php echo $page_title; ?></h2>

            <form method="post" class="action_form">
                <div class="form-group">
                    <input type="password" name="old_password" class="cus_field" value="<?php echo set_value('old_password') ?>" placeholder="Old Password">
                </div>
                <div class="form-group">
                    <input type="password" name="new_password" class="cus_field" value="<?php echo set_value('new_password') ?>" placeholder="New Password">
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" class="cus_field" value="<?php echo set_value('confirm_password') ?>" placeholder="Confirm Password">
                </div>
                <input type="submit" name="submit" value="Change Password" class="roboto_condensed cus_button">
            </form>
        </div>
    </div>
</div>