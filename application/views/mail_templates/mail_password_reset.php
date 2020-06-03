<p style="margin:0 0 16px">Hi <?php echo $user->first_name; ?>,</p>
<p style="margin:0 0 16px">Someone has requested a new password for the following account on <?php echo $this->config->item('site_title'); ?>:</p>
<p style="margin:0 0 16px">Username: <?php echo $user->user_name; ?></p>
<p style="margin:0 0 16px">If you didn't make this request, just ignore this email. If you'd like to proceed:</p>
<p style="margin:0 0 16px">
    <a href="<?php echo site_url().'auth/reset_password/'.$code; ?>" style="color:#141414;font-weight:normal;text-decoration:underline" target="_blank">Click here to reset your password</a>
</p>
<p style="margin:0 0 16px">Thanks for reading.</p>
