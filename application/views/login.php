<div class="row">
    <div class="col-md-12">
        <div class="user_login">
            <h2 class="montserrat"><?php echo $page_title; ?></h2>
            <?php
            $attributes = array('role' => 'form', 'method' => 'post', 'class' => 'action_form');
            echo form_open('auth/login', $attributes);
            ?>

            <div class="form-group">
                <input name="user_name" class="cus_field" value="<?php echo set_value('user_name') ?>" placeholder="Username" type="text" required autofocus>
            </div>
            <div class="form-group">
                <input name="password" class="cus_field" placeholder="Password" type="password" value="" required>
            </div>
            <div class="form-group">
                <input type="submit" name="submit" value="Login" class="roboto_condensed cus_button">
            </div>
            <div class="lost_password">
                <a href="<?php echo site_url('auth/forget_password'); ?>">Lost your password?</a>
            </div>

            <?php
            echo form_close();
            ?>
        </div>
    </div>
</div>