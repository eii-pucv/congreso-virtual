<?php

use Illuminate\Http\Request;

Auth::routes(['verify' => true]);

Route::group(['prefix' => 'auth'], function () {

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });

    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::get('signup/activate/{token}', 'AuthController@signupActivate');
    Route::get('/{provider}', 'AuthController@redirectToProvider');
    Route::get('/{provider}/callback', 'AuthController@handleProviderCallback');
});

Route::group([
    'namespace' => 'Auth',
    'middleware' => 'api',
    'prefix' => 'password'
], function () {
    Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');
});

Route::group(['prefix' => 'storage'], function () {
    Route::get('/public/{storedBasename}', 'StorageController@showFileOfPublic');
    Route::get('/avatars/{user}/{storedBasename}', 'StorageController@showAvatarOfUser');
    Route::get('/users/{user}/{storedBasename}', 'StorageController@showFileOfUser');
    Route::get('/projects/{project}/{storedBasename}', 'StorageController@showFileOfProject');
    Route::get('/consultations/{publicConsultation}/{storedBasename}', 'StorageController@showFileOfPublicConsultation');
    Route::get('/comments/{comment}/{storedBasename}', 'StorageController@showFileOfComment');
});

Route::get('/ngram', function(Request $request) {
    $client = new \GuzzleHttp\Client();
    $request = $client->request(
        'GET',
        env('APP_ANALYTIC_URL') . '/ngram',
        [
            'query' => [
                'words' => $request->get('min_words'),
                'project_id' => $request->get('project_id')
            ]
        ]
    );
    $response = $request->getBody();
    return $response;
});

Route::get('/topicmodel', function(Request $request) {
    set_time_limit(1800);

    $client = new \GuzzleHttp\Client();
    $request = $client->request(
        'GET',
        env('APP_ANALYTIC_URL') . '/topicmodel',
        [
            'query' => [
                'project_id' => $request->get('project_id')
            ]
        ]
    );
    $response = $request->getBody();
    return $response;
});

Route::get('/wordcloud', 'WordCloudController@show');


Route::group(['middleware' => ['auth:api', 'has.roles:ADMIN,USER']], function() {

    Route::group(['prefix' => 'projects'], function () {
        Route::post('/{project}/commentary', 'ProjectController@storeComment');
        Route::post('/{project}/vote', 'ProjectController@storeVote');
    });

    Route::group(['prefix' => 'articles'], function () {
        Route::post('/{article}/commentary', 'ArticleController@storeComment');
        Route::post('/{article}/vote', 'ArticleController@storeVote');
    });

    Route::group(['prefix' => 'ideas'], function () {
        Route::post('/{idea}/commentary', 'IdeaController@storeComment');
        Route::post('/{idea}/vote', 'IdeaController@storeVote');
    });

    Route::group(['prefix' => 'proposals'], function () {
        Route::post('/', 'ProposalController@store');
        Route::put('/{proposal}', 'ProposalController@update');
        Route::delete('/{proposal}', 'ProposalController@destroy');
        Route::post('/{proposal}/urgency', 'ProposalController@storeUrgency');
    });

    Route::group(['prefix' => 'consultations'], function () {
        Route::post('/{publicConsultation}/commentary', 'PublicConsultationController@storeComment');
        Route::post('/{publicConsultation}/vote', 'PublicConsultationController@storeVote');
    });

    Route::group(['prefix' => 'comments'], function () {
        Route::post('/', 'CommentController@store');
        Route::put('/{comment}', 'CommentController@update');
        Route::delete('/{comment}', 'CommentController@destroy');
        Route::post('/{comment}/vote', 'CommentController@storeVote');
        Route::post('/{comment}/files', 'CommentController@storeFilesRequest');
        Route::delete('/{comment}/file/{file}', 'CommentController@removeFileRequest');
    });

    Route::group(['prefix' => 'denounces'], function () {
        Route::post('/', 'DenounceController@store');
        Route::put('/{denounce}', 'DenounceController@update');
    });

    Route::group(['prefix' => 'votes'], function () {
        Route::post('/', 'VoteController@store');
        Route::put('/{vote}', 'VoteController@update');
    });

    Route::group(['prefix' => 'urgencies'], function () {
        Route::post('/', 'UrgencyController@store');
        Route::put('/{urgency}', 'UrgencyController@update');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/', 'UserController@index');
        Route::put('/{user}', 'UserController@update');
        Route::patch('/password', 'UserController@updatePassword');
        Route::post('/{user}/avatar', 'UserController@updateAvatar');
        Route::delete('/{user}/avatar', 'UserController@removeAvatar');
        Route::post('/{user}/term', 'UserController@associateTerm');
        Route::post('/{user}/terms', 'UserController@associateTerms');
        Route::delete('/{user}/terms', 'UserController@dissociateAllTerms');
    });

    Route::group(['prefix' => 'locations_orgs'], function () {
        Route::post('/', 'LocationOrgController@store');
        Route::put('/{locationOrg}', 'LocationOrgController@update');
        Route::delete('/{locationOrg}', 'LocationOrgController@destroy');
        Route::patch('/{locationOrg}/undelete', 'LocationOrgController@undelete');
    });

    Route::group(['prefix' => 'members_orgs'], function () {
        Route::post('/', 'MemberOrgController@store');
        Route::put('/{memberOrg}', 'MemberOrgController@update');
        Route::delete('/{memberOrg}', 'MemberOrgController@destroy');
        Route::patch('/{memberOrg}/undelete', 'MemberOrgController@undelete');
    });
});

Route::group(['middleware' => ['auth:api', 'has.roles:ADMIN']], function() {

    Route::group(['prefix' => 'projects'], function () {
        Route::post('/', 'ProjectController@store');
        Route::put('/{project}', 'ProjectController@update');
        Route::delete('/{project}', 'ProjectController@destroy');
        Route::patch('/{project}/undelete', 'ProjectController@undelete');
        Route::patch('/{project}/is_enabled', 'ProjectController@updateIsEnabled');
        Route::post('/{project}/image', 'ProjectController@updateImage');
        Route::delete('/{project}/image', 'ProjectController@removeImage');
        Route::get('/{project}/users_with_project_terms', 'ProjectController@usersWithProjectTermsRequest');
        Route::get('/{project}/users_participants', 'ProjectController@usersParticipantsOnProjectRequest');
        Route::post('/{project}/terms', 'ProjectController@associateTerms');
        Route::delete('/{project}/terms', 'ProjectController@dissociateAllTerms');
        Route::post('/{project}/stopwords', 'ProjectController@associateStopwords');
        Route::delete('/{project}/stopwords', 'ProjectController@dissociateAllStopwords');
        Route::post('/{project}/files', 'ProjectController@storeFilesRequest');
        Route::delete('/{project}/file/{file}', 'ProjectController@removeFileRequest');
        Route::delete('/{project}/files', 'ProjectController@removeFilesRequest');
        Route::get('/{project}/notify_email', 'ProjectController@notifyByEmailRequest');
        Route::get('/sorted_by', 'ProjectController@indexSortedBy');
        Route::get('/{project}/report', 'ProjectController@report');
    });

    Route::group(['prefix' => 'articles'], function () {
        Route::post('/', 'ArticleController@store');
        Route::put('/{article}', 'ArticleController@update');
        Route::delete('/{article}', 'ArticleController@destroy');
        Route::patch('/{article}/undelete', 'ArticleController@undelete');
    });

    Route::group(['prefix' => 'ideas'], function () {
        Route::post('/', 'IdeaController@store');
        Route::put('/{idea}', 'IdeaController@update');
        Route::delete('/{idea}', 'IdeaController@destroy');
        Route::patch('/{idea}/undelete', 'IdeaController@undelete');
    });

    Route::group(['prefix' => 'proposals'], function () {
        Route::patch('/{proposal}/undelete', 'ProposalController@undelete');
    });

    Route::group(['prefix' => 'consultations'], function () {
        Route::post('/', 'PublicConsultationController@store');
        Route::put('/{publicConsultation}', 'PublicConsultationController@update');
        Route::delete('/{publicConsultation}', 'PublicConsultationController@destroy');
        Route::patch('/{publicConsultation}/undelete', 'PublicConsultationController@undelete');
        Route::post('/{publicConsultation}/image', 'PublicConsultationController@updateImage');
        Route::delete('/{publicConsultation}/image', 'PublicConsultationController@removeImage');
        Route::post('/{publicConsultation}/terms', 'PublicConsultationController@associateTerms');
        Route::delete('/{publicConsultation}/terms', 'PublicConsultationController@dissociateAllTerms');
    });

    Route::group(['prefix' => 'comments'], function () {
        Route::patch('/{comment}/undelete', 'CommentController@undelete');
        Route::patch('/{comment}/state', 'CommentController@updateState');
        Route::patch('/{comment}/perception', 'CommentController@updatePerception');
        Route::get('/{comment}/denounces', 'CommentController@denounces');
        Route::get('/sorted_by', 'CommentController@indexSortedByRequest');
    });

    Route::group(['prefix' => 'denounces'], function () {
        Route::get('/', 'DenounceController@index');
        Route::get('/{denounce}', 'DenounceController@show');
        Route::delete('/{denounce}', 'DenounceController@destroy');
        Route::patch('/{denounce}/undelete', 'DenounceController@undelete');
        Route::get('/{denounce}/comment', 'DenounceController@comment');
        Route::get('/{denounce}/user', 'DenounceController@user');
    });

    Route::group(['prefix' => 'votes'], function () {
        Route::delete('/{vote}', 'VoteController@destroy');
    });

    Route::group(['prefix' => 'urgencies'], function () {
        Route::delete('/{urgency}', 'UrgencyController@destroy');
    });

    Route::group(['prefix' => 'taxonomies'], function () {
        Route::post('/', 'TaxonomyController@store');
        Route::put('/{taxonomy}', 'TaxonomyController@update');
        Route::delete('/{taxonomy}', 'TaxonomyController@destroy');
        Route::patch('/{taxonomy}/undelete', 'TaxonomyController@undelete');
    });

    Route::group(['prefix' => 'terms'], function () {
        Route::post('/', 'TermController@store');
        Route::put('/{term}', 'TermController@update');
        Route::delete('/{term}', 'TermController@destroy');
        Route::patch('/{term}/undelete', 'TermController@undelete');
    });

    Route::group(['prefix' => 'taxonomies_terms'], function () {
        Route::post('/', 'TaxonomyTermController@store');
        Route::delete('/{taxonomyTerm}', 'TaxonomyTermController@destroy');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::post('/', 'UserController@store');
        Route::delete('/{user}', 'UserController@destroy');
        Route::patch('/{user}/undelete', 'UserController@undelete');
        Route::get('/{user}/denounces', 'UserController@denounces');
    });

    Route::group(['prefix' => 'pages'], function () {
        Route::post('/', 'PageController@store');
        Route::put('/{page}', 'PageController@update');
        Route::delete('/{page}', 'PageController@destroy');
        Route::patch('/{page}/undelete', 'PageController@undelete');
        Route::post('/{page}/terms', 'PageController@associateTerms');
        Route::delete('/{page}/terms', 'PageController@dissociateAllTerms');
    });

    Route::group(['prefix' => 'settings'], function () {
        Route::post('/', 'SettingController@store');
        Route::put('/{setting}', 'SettingController@update');
        Route::put('/', 'SettingController@updateMultiple');
        Route::delete('/{setting}', 'SettingController@destroy');
        Route::delete('/', 'SettingController@destroyMultiple');
    });

    Route::group(['prefix' => 'stopwords'], function () {
        Route::post('/', 'StopwordController@store');
        Route::put('/{stopword}', 'StopwordController@update');
        Route::put('/', 'StopwordController@updateMultiple');
        Route::delete('/{stopword}', 'StopwordController@destroy');
        Route::delete('/', 'StopwordController@destroyMultiple');
    });

    Route::group(['prefix' => 'stopword_types'], function () {
        Route::post('/', 'StopwordTypeController@store');
        Route::put('/{stopwordType}', 'StopwordTypeController@update');
        Route::delete('/{stopwordType}', 'StopwordTypeController@destroy');
    });

    Route::group(['prefix' => 'offensive_words'], function () {
        Route::post('/', 'OffensiveWordController@store');
        Route::get('/', 'OffensiveWordController@index');
        Route::get('/{offensiveword}', 'OffensiveWordController@show');
        Route::put('/{offensiveword}', 'OffensiveWordController@update');
        Route::delete('/{offensiveword}', 'OffensiveWordController@destroy');
    });
});

Route::group(['middleware' => ['is.auth:api']], function() {
    Route::group(['prefix' => 'projects'], function () {
        Route::get('/', 'ProjectController@index');
        Route::get('/{project}', 'ProjectController@show');
        Route::get('/{project}/image', 'ProjectController@image');
        Route::get('/{project}/articles', 'ProjectController@articles');
        Route::get('/{project}/ideas', 'ProjectController@ideas');
        Route::get('/{project}/comments', 'ProjectController@comments');
        Route::get('/{project}/votes', 'ProjectController@votes');
        Route::get('/{project}/terms', 'ProjectController@terms');
        Route::get('/{project}/stopwords', 'ProjectController@stopwords');
        Route::get('/{project}/files', 'ProjectController@files');
        Route::get('/{project}/vote', 'ProjectController@showUserVote');
    });

    Route::group(['prefix' => 'articles'], function () {
        Route::get('/', 'ArticleController@index');
        Route::get('/{article}', 'ArticleController@show');
        Route::get('/{article}/project', 'ArticleController@project');
        Route::get('/{article}/comments', 'ArticleController@comments');
        Route::get('/{article}/votes', 'ArticleController@votes');
        Route::get('/{article}/vote', 'ArticleController@showUserVote');
    });

    Route::group(['prefix' => 'ideas'], function () {
        Route::get('/', 'IdeaController@index');
        Route::get('/{idea}', 'IdeaController@show');
        Route::get('/{idea}/project', 'IdeaController@project');
        Route::get('/{idea}/comments', 'IdeaController@comments');
        Route::get('/{idea}/votes', 'IdeaController@votes');
        Route::get('/{idea}/vote', 'IdeaController@showUserVote');
    });

    Route::group(['prefix' => 'proposals'], function () {
        Route::get('/', 'ProposalController@index');
        Route::get('/{proposal}', 'ProposalController@show');
        Route::get('/{proposal}/user', 'ProposalController@user');
        Route::get('/{proposal}/urgencies', 'ProposalController@urgencies');
        Route::get('/{proposal}/urgency', 'ProposalController@showUserUrgency');
    });

    Route::group(['prefix' => 'consultations'], function () {
        Route::get('/', 'PublicConsultationController@index');
        Route::get('/{publicConsultation}', 'PublicConsultationController@show');
        Route::get('/{publicConsultation}/image', 'PublicConsultationController@image');
        Route::get('/{publicConsultation}/comments', 'PublicConsultationController@comments');
        Route::get('/{publicConsultation}/votes', 'PublicConsultationController@votes');
        Route::get('/{publicConsultation}/terms', 'PublicConsultationController@terms');
        Route::get('/{publicConsultation}/vote', 'PublicConsultationController@showUserVote');
    });

    Route::group(['prefix' => 'comments'], function () {
        Route::get('/', 'CommentController@index');
        Route::get('/{comment}', 'CommentController@show');
        Route::get('/{comment}/user', 'CommentController@user');
        Route::get('/{comment}/comments', 'CommentController@comments');
        Route::get('/{comment}/votes', 'CommentController@votes');
        Route::get('/{comment}/files', 'CommentController@files');
        Route::get('/{comment}/vote', 'CommentController@showUserVote');
    });

    Route::group(['prefix' => 'votes'], function () {
        Route::get('/', 'VoteController@index');
        Route::get('/{vote}', 'VoteController@show');
    });

    Route::group(['prefix' => 'urgencies'], function () {
        Route::get('/', 'UrgencyController@index');
        Route::get('/{urgency}', 'UrgencyController@show');
    });

    Route::group(['prefix' => 'taxonomies'], function () {
        Route::get('/', 'TaxonomyController@index');
        Route::get('/{taxonomy}', 'TaxonomyController@show');
        Route::get('/{taxonomy}/terms', 'TaxonomyController@terms');
    });

    Route::group(['prefix' => 'terms'], function () {
        Route::get('/', 'TermController@index');
        Route::get('/{term}', 'TermController@show');
        Route::get('/{term}/taxonomies', 'TermController@taxonomies');
    });

    Route::group(['prefix' => 'taxonomies_terms'], function () {
        Route::get('/', 'TaxonomyTermController@index');
        Route::get('/{taxonomyTerm}', 'TaxonomyTermController@show');
        Route::get('/{taxonomyTerm}/taxonomy', 'TaxonomyTermController@taxonomy');
        Route::get('/{taxonomyTerm}/term', 'TaxonomyTermController@term');
    });

    Route::group(['prefix' => 'users'], function () {
        Route::get('/{user}', 'UserController@show');
        Route::get('/{user}/avatar', 'UserController@avatar');
        Route::get('/{user}/locations_orgs', 'UserController@locationOrgs');
        Route::get('/{user}/members_orgs', 'UserController@memberOrgs');
        Route::get('/{user}/comments', 'UserController@comments');
        Route::get('/{user}/votes', 'UserController@votes');
        Route::get('/{user}/urgencies', 'UserController@urgencies');
        Route::get('/{user}/proposals', 'UserController@proposals');
        Route::get('/{user}/terms', 'UserController@terms');
    });

    Route::group(['prefix' => 'locations_orgs'], function () {
        Route::get('/', 'LocationOrgController@index');
        Route::get('/{locationOrg}', 'LocationOrgController@show');
    });

    Route::group(['prefix' => 'members_orgs'], function () {
        Route::get('/', 'MemberOrgController@index');
        Route::get('/{memberOrg}', 'MemberOrgController@show');
    });

    Route::group(['prefix' => 'pages'], function () {
        Route::get('/', 'PageController@index');
        Route::get('/{page}', 'PageController@show');
        Route::get('/{page}/terms', 'PageController@terms');
    });

    Route::group(['prefix' => 'search'], function () {
        Route::get('/', 'SearchController@index');
    });

    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', 'SettingController@index');
        Route::get('/{setting}', 'SettingController@show');
    });

    Route::group(['prefix' => 'stopwords'], function () {
        Route::get('/', 'StopwordController@index');
        Route::get('/{stopword}', 'StopwordController@show');
    });

    Route::group(['prefix' => 'stopword_types'], function () {
        Route::get('/', 'StopwordTypeController@index');
        Route::get('/{stopwordType}', 'StopwordTypeController@show');
    });
});
