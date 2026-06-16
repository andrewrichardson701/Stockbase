<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LdapController;
use App\Http\Controllers\SmtpController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SSOController;

// Admin pages
Route::middleware(['auth', 'check.permission:admin'])->group(function () { // Admin pages - locked behind the admin or root permission
    // admin routes
    Route::get('/admin/{setting?}', [AdminController::class, 'index']) // admin page - setting from the left hand menu
        ->where('setting', '[a-z\-]+')
        ->name('admin'); 
    Route::get('/admin.smtpTemplate', [SmtpController::class, 'template'])->name('admin.smtpTemplate'); // view SMTP template
    Route::get('/admin.emailTemplatePreview', [SmtpController::class, 'emailTemplatePreview'])->name('admin.emailTemplatePreview'); // preview the email template
    Route::get('/admin.getEmailTemplateUrl', [SmtpController::class, 'getEmailTemplateUrl'])->name('admin.getEmailTemplateUrl'); // preview the email template

    // POST REQUESTS
    Route::post('/admin.globalSettings', [AdminController::class, 'updateConfigSettings'])->name('admin.globalSettings'); // Adjust global settings
    Route::post('/admin.toggleFooter', [AdminController::class, 'toggleFooter'])->name('admin.toggleFooter'); // Adjust toggle footer AJAX
    Route::post('/admin.toggleAuth', [AdminController::class, 'toggleAuth'])->name('admin.toggleAuth'); // Adjust toggle footer AJAX
    Route::post('/admin.userSettings', [AdminController::class, 'userSettings'])->name('admin.userSettings'); // Adjust user Settings
    Route::post('/admin.attributeSettings', [AdminController::class, 'attributeSettings'])->name('admin.attributeSettings'); // Adjust Attribute Settings
    Route::post('/admin.stockManagementSettings', [AdminController::class, 'stockManagementSettings'])->name('admin.stockManagementSettings'); // Adjust Stock Management Settings
    Route::post('/admin.ldapSettings', [AdminController::class, 'ldapSettings'])->name('admin.ldapSettings'); // Adjust LDAP settings
    Route::post('/admin.ldapTest', [LdapController::class, 'testLdap'])->name('admin.ldapTest'); // test LDAP
    Route::post('/admin.ssoToggle', [AdminController::class, 'ssoToggle'])->name('admin.ssoToggle'); // Adjust SSO settings
    Route::post('/admin.ssoSettings', [SSOController::class, 'saveSettings'])->name('admin.ssoSettings'); // Adjust SSO settings
    Route::post('/admin.smtpSettings', [AdminController::class, 'smtpSettings'])->name('admin.smtpSettings'); // Adjust SMTP settings
    Route::post('/admin.smtpTest', [SmtpController::class, 'smtpTest'])->name('admin.smtpTest'); // SMTP test
    Route::post('/admin.toggleEmailNotification', [AdminController::class, 'toggleEmailNotification'])->name('admin.toggleEmailNotification'); // Adjust Notification settings
    Route::post('/admin.stockLocationSettings', [AdminController::class, 'stockLocationSettings'])->name('admin.stockLocationSettings'); // Adjust Stock Location settings
    Route::post('/admin.imageManagementSettings', [AdminController::class, 'imageManagementSettings'])->name('admin.imageManagementSettings'); // Adjust Image Management settings
    Route::post('/admin.killUserSession', [AdminController::class, 'killUserSession'])->name('admin.killUserSession'); // kill a user session
    Route::post('/admin.emailTemplate', [AdminController::class, 'emailTemplate'])->name('admin.emailTemplate'); // change an email template
    Route::post('/admin.addLocalUser', [AdminController::class, 'addLocalUser'])->name('admin.addLocalUser'); // add Local User
    Route::post('/admin.webhookSettings', [AdminController::class, 'webhookSettings'])->name('admin.webhookSettings'); // Adjust Webhook settings
    Route::post('/admin.webhookTest', [WebhookController::class, 'webhookTest'])->name('admin.webhookTest'); // Webook test
    Route::post('/admin.toggleWebhookNotification', [AdminController::class, 'toggleWebhookNotification'])->name('admin.toggleWebhookNotification'); // Adjust Notification settings
    Route::post('/admin.webhookTemplate', [AdminController::class, 'webhookTemplate'])->name('admin.webhookTemplate'); // change an webhook template
    
    Route::get('/debug', [AdminController::class, 'debug'])->name('debug'); // Debug info
});