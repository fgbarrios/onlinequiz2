<?php

use Illuminate\Support\Facades\Route;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use App\WebSocket\WebSocketServer;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

    Auth::routes();
    
    Route::get('start-socket', function(){
        Artisan::call('websocket:init');
    });

    Route::get('/phpinfo', function() {

        phpinfo();

    });

    Route::get('/', [App\Http\Controllers\Admin\HomeController::class, 'index'])->name('index');

    # SMS Integration

    Route::any('receive-sms', [App\Http\Controllers\Visitor\VisitorController::class, 'get_visitor_request'])->name('receive-sms');

    Route::any('send-websockets/{activity_id}', [App\Http\Controllers\Visitor\VisitorController::class, 'send_websockets'])->name('send-websockets');

    Route::get('poll-result/{id}', [App\Http\Controllers\Admin\ActivityController::class, 'poll_result'])->middleware('allowframe')->name('poll-result');

    Route::get('poll-result-multiple/{id}/{sub_id}', [App\Http\Controllers\Admin\ActivityController::class, 'poll_result_multiple'])->middleware('allowframe')->name('poll-result-multiple');

    # Visitors

    Route::get('/visit/{username}', [App\Http\Controllers\Visitor\VisitorController::class, 'visit'])->name('visit');
    Route::post('/save-visitors-details', [App\Http\Controllers\Visitor\VisitorController::class, 'save_visitors_details'])->name('save-visitors-details');
    Route::get('/vote-page', [App\Http\Controllers\Visitor\VisitorController::class, 'vote_page'])->name('vote-page');
    Route::post('/add-selected-option', [App\Http\Controllers\Visitor\VisitorController::class, 'add_selected_option'])->name('add-selected-option');
    Route::post('/remove-selected-option', [App\Http\Controllers\Visitor\VisitorController::class, 'remove_selected_option'])->name('remove-selected-option');
    Route::post('/save-visitors-response', [App\Http\Controllers\Visitor\VisitorController::class, 'save_visitors_response'])->name('save-visitors-response');
    Route::post('/save-visitors-response-multiple', [App\Http\Controllers\Visitor\VisitorController::class, 'save_visitors_response_multiple'])->name('save-visitors-response-multiple');
    Route::get('/thank-you', [App\Http\Controllers\Visitor\VisitorController::class, 'thank_you'])->name('thank-you');
    # Change Language

    Route::get('/change-language/{lang}', [App\Http\Controllers\Admin\HomeController::class, 'change_language'])->name('change_language');

    # Admin Related Routes

    Route::get('/forget-password', [App\Http\Controllers\Admin\HomeController::class, 'forget_password'])->name('forget-password');


    Route::group(['middleware' => ['auth']], function () {

        # Dashboard

        Route::get('/dashboard', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('dashboard');
        Route::get('/invite-visitors', [App\Http\Controllers\Admin\HomeController::class, 'invite_visitors'])->name('invite-visitors');
        Route::post('/invite-visitors', [App\Http\Controllers\Admin\HomeController::class, 'invite_visitors_via_email'])->name('invite-visitors');

        # Account

        Route::get('/profile', [App\Http\Controllers\Admin\HomeController::class, 'profile'])->name('profile');
        Route::post('update-profile', [App\Http\Controllers\Admin\HomeController::class, 'update_profile'])->name('update-profile');
        Route::get('change-password', [App\Http\Controllers\Admin\HomeController::class, 'change_password'])->name('change-password');
        Route::post('change-password', [App\Http\Controllers\Admin\HomeController::class, 'change_new_password'])->name('change-password');

        # Activity

        Route::get('list-activity', [App\Http\Controllers\Admin\ActivityController::class, 'list_activity'])->name('list-activity');
        Route::get('add-activity', [App\Http\Controllers\Admin\ActivityController::class, 'add_activity'])->name('add-activity');
        Route::post('save-activity', [App\Http\Controllers\Admin\ActivityController::class, 'save_activity'])->name('save-activity');
        Route::get('edit-activity/{id}', [App\Http\Controllers\Admin\ActivityController::class, 'edit_activity'])->name('edit-activity');
        Route::post('upload-option-image', [App\Http\Controllers\Admin\ActivityController::class, 'upload_option_image'])->name('upload-option-image');
        Route::post('change-activity-status', [App\Http\Controllers\Admin\ActivityController::class, 'change_activity_status'])->name('change-activity-status');
        Route::post('update-activity-sort-order', [App\Http\Controllers\Admin\ActivityController::class, 'update_activity_sort_order'])->name('update-activity-sort-order');
        Route::post('delete-activity', [App\Http\Controllers\Admin\ActivityController::class, 'delete_activity'])->name('delete-activity');
        Route::post('clone-activity', [App\Http\Controllers\Admin\ActivityController::class, 'clone_activity'])->name('clone-activity');
        Route::get('list-trash', [App\Http\Controllers\Admin\ActivityController::class, 'list_trash'])->name('list-trash');
        Route::post('trash-action', [App\Http\Controllers\Admin\ActivityController::class, 'trash_action'])->name('trash-action');
        Route::post('clear-activity-response', [App\Http\Controllers\Admin\ActivityController::class, 'clear_activity_response'])->name('clear-activity-response');
        Route::post('load-activity-data', [App\Http\Controllers\Admin\ActivityController::class, 'load_activity_data_sort'])->name('load-activity-data');

        # Response
        
        Route::get('see-graph-response/{id}', [App\Http\Controllers\Admin\ActivityController::class, 'see_graph_response'])->name('see-graph-response');
        Route::get('see-response/{id}', [App\Http\Controllers\Admin\ActivityController::class, 'see_response'])->name('see-response');
        Route::get('download-excel/{id}', [App\Http\Controllers\Admin\ActivityController::class, 'download_excel'])->name('download-excel');
        Route::get('print-response/{id}', [App\Http\Controllers\Admin\ActivityController::class, 'print_response'])->name('print-response');
        Route::post('share-responses', [App\Http\Controllers\Admin\ActivityController::class, 'share_responses'])->name('share-responses');

        # Settings

        Route::get('activity-settings', [App\Http\Controllers\Admin\SettingsController::class, 'activity_settings'])->name('activity-settings');
        Route::post('save-activity-settings', [App\Http\Controllers\Admin\SettingsController::class, 'save_activity_settings'])->name('save-activity-settings');

        # General Settings

        Route::get('general-settings', [App\Http\Controllers\Admin\SettingsController::class, 'general_settings'])->name('general-settings');
        Route::post('save-general-settings', [App\Http\Controllers\Admin\SettingsController::class, 'save_general_settings'])->name('save-general-settings');

        # Testing

        Route::get('load-activity-data', [App\Http\Controllers\Admin\ActivityController::class, 'load_activity_data'])->name('load-activity-data');

        Route::post('send-polling-results', [App\Http\Controllers\Admin\ActivityController::class, 'send_polling_results'])->name('send-polling-results');


    }); 