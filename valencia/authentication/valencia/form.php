<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>

<form method='post'
      action='<?php echo View::url('/login', 'authenticate', $this->getAuthenticationTypeHandle()) ?>'>
    <div class="form-group concrete-login">
        <h3><?php echo t('Sign in to the Energy Data Hub')?></h3>
        <hr>
    </div>
    <div class="form-group">
		<label class="control-label"><?php echo Config::get('concrete.user.registration.email_registration') ? t('Email Address') : t('Username')?></label>	
        <input name="uName" class="form-control col-sm-12"
               placeholder="<?php echo Config::get('concrete.user.registration.email_registration') ? t('Email Address') : t('Username')?>" />
    </div>

    <div class="form-group">
		<label class="control-label"><br/><?php echo t('Password')?></label>
        <input name="uPassword" class="form-control" type="password"
               placeholder="Password" />
    </div>


    <?php
    if (isset($locales) && is_array($locales) && count($locales) > 0) {
        ?>
        <div class="form-group">
            <label for="USER_LOCALE" class="control-label"><?php echo t('Language') ?></label>
            <?php echo $form->select('USER_LOCALE', $locales) ?>
        </div>
    <?php
    }
    ?>

    <div class="form-group">
        <button class="btn btn-primary"><?php echo t('Log in') ?></button> 
    </div>

    <script type="text/javascript">
        document.querySelector('input[name=uName]').focus();
    </script>
    <?php Loader::helper('validation/token')->output('login_' . $this->getAuthenticationTypeHandle()); ?>
</form>