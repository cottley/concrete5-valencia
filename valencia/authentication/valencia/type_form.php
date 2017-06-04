<?php defined('C5_EXECUTE') or die('Access denied.'); ?>
<div class='form-group'>
    <?php echo $form->label('ldaphost01', t('LDAP Host 01'))?>
    <?php echo $form->text('ldaphost01', $ldaphost01)?>
</div>
<div class='form-group'>
    <?php echo $form->label('ldapdn01', t('LDAP Base DN 01'))?>
    <?php echo $form->text('ldapdn01', $ldapdn01)?>
</div>
<div class='form-group'>
    <?php echo $form->label('ldapdomain01', t('LDAP Domain 01 ex: domain\\user'))?>
    <?php echo $form->text('ldapdomain01', $ldapdomain01)?>
</div>
<div class='form-group'>
    <?php echo $form->label('ldapport01', t('LDAP Port 01 ex: 389 for unencrypted, 636 for SSL/TLS'))?>
    <?php echo $form->text('ldapport01', $ldapport01)?>
</div>
<div class='form-group'>
    <?php echo $form->label('ldapssl01', t('Use LDAP SSL (do not check if not using encryption or using TLS)'))?>
    <?php echo $form->checkbox('ldapssl01', 1, $ldapssl01)?>
</div>
<div class='form-group'>
    <?php echo $form->label('ldaptls01', t('Use LDAP TLS (do not check if not using encryption or using SSL)'))?>
    <?php echo $form->checkbox('ldaptls01', 1, $ldaptls01)?>
</div>

<div class='form-group'>
    <?php echo $form->label('ldaphost02', t('LDAP Host 02'))?>
    <?php echo $form->text('ldaphost02', $ldaphost02)?>
</div>
<div class='form-group'>
    <?php echo $form->label('ldapdn02', t('LDAP Base DN 02'))?>
    <?php echo $form->text('ldapdn02', $ldapdn02)?>
</div>
<div class='form-group'>
    <?php echo $form->label('ldapdomain02', t('LDAP Domain 02 ex: domain\\user'))?>
    <?php echo $form->text('ldapdomain02', $ldapdomain02)?>
</div>
<div class='form-group'>
    <?php echo $form->label('ldapport02', t('LDAP Port 02 ex: 389 for unencrypted, 636 for SSL/TLS'))?>
    <?php echo $form->text('ldapport02', $ldapport02)?>
</div>

<div class='form-group'>
    <?php echo $form->label('ldapssl02', t('Use LDAP SSL (do not check if not using encryption or using TLS)'))?>
    <?php echo $form->checkbox('ldapssl02', 1, $ldapssl02)?>
</div>
<div class='form-group'>
    <?php echo $form->label('ldaptls02', t('Use LDAP TLS (do not check if not using encryption or using SSL)'))?>
    <?php echo $form->checkbox('ldaptls02', 1, $ldaptls02)?>
</div>

<div class='form-group'>
    <?php echo $form->label('adLDAP Version To use', t('adLDAP Version ex: 4.0.4'))?>
    <?php echo $form->text('adldapversion', $adldapversion)?>
</div>