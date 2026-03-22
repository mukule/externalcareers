<?php

use CodeIgniter\Router\RouteCollection;

$routes->get('/', 'Home::home');

$routes->get('/home', 'Home::home');


$routes->get('/login', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::store');

$routes->get('/register', 'AuthController::create_account');
$routes->post('/register', 'AuthController::register');

$routes->get('/password', 'AuthController::forgotPassword');
$routes->post('/forgot-password', 'AuthController::sendResetLink');

$routes->get('/logout', 'AuthController::logout');

$routes->get('activate/(:any)', 'AuthController::activate/$1');

// Home page (general auth)
$routes->get('/index', 'Home::index', ['filter' => 'auth']);


// Change password routes
$routes->get('auth/change-password', 'AuthController::changePassword');
$routes->post('auth/update-password', 'AuthController::updatePassword');

// Job detail page
$routes->get('jobs/(:segment)', 'Home::jobDetail/$1');
$routes->get('job_type/(:segment)', 'Home::jobsByType/$1');




$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {

    // Admin dashboard
    $routes->get('/', 'Home::adminDashboard');
    $routes->get('user-status/(:num)/(:any)', 'Home::toggleUserStatus/$1/$2');
    $routes->get('users/(:segment)', 'Home::userDetail/$1');
    $routes->get('user-del/(:num)/delete', 'Home::deleteUser/$1');

    // Job Types CRUD
    $routes->get('job-types', 'Admin\JobTypeController::index');
    $routes->get('job-types/create', 'Admin\JobTypeController::create');
    $routes->post('job-types/store', 'Admin\JobTypeController::store');
    $routes->get('job-types/edit/(:segment)', 'Admin\JobTypeController::edit/$1');
    $routes->post('job-types/update', 'Admin\JobTypeController::update');
    $routes->get('job-types/delete/(:segment)', 'Admin\JobTypeController::delete/$1');

    // Job Disciplines CRUD
    $routes->get('job-disciplines', 'Admin\JobDisciplineController::index');
    $routes->get('job-disciplines/create', 'Admin\JobDisciplineController::create');
    $routes->post('job-disciplines/store', 'Admin\JobDisciplineController::store');
    $routes->get('job-disciplines/edit/(:segment)', 'Admin\JobDisciplineController::edit/$1');
    $routes->post('job-disciplines/update', 'Admin\JobDisciplineController::update');
    $routes->get('job-disciplines/delete/(:segment)', 'Admin\JobDisciplineController::delete/$1');

    // Certifying Bodies CRUD
    $routes->get('certifying-bodies', 'Admin\CertifyingBodyController::index');
    $routes->get('certifying-bodies/create', 'Admin\CertifyingBodyController::create');
    $routes->post('certifying-bodies/store', 'Admin\CertifyingBodyController::store');
    $routes->get('certifying-bodies/edit/(:segment)', 'Admin\CertifyingBodyController::edit/$1');
    $routes->post('certifying-bodies/update', 'Admin\CertifyingBodyController::update');
    $routes->get('certifying-bodies/delete/(:segment)', 'Admin\CertifyingBodyController::delete/$1');

    // Certifications CRUD
    $routes->get('certifications', 'Admin\CertificationsController::index');
    $routes->get('certifications/create', 'Admin\CertificationsController::create');
    $routes->post('certifications/store', 'Admin\CertificationsController::store');
    $routes->get('certifications/edit/(:segment)', 'Admin\CertificationsController::edit/$1');
    $routes->post('certifications/update', 'Admin\CertificationsController::update');
    $routes->get('certifications/delete/(:segment)', 'Admin\CertificationsController::delete/$1');

    // Fields of Study CRUD
    $routes->get('fields-of-study', 'Admin\FieldOfStudyController::index');
    $routes->get('fields-of-study/create', 'Admin\FieldOfStudyController::create');
    $routes->post('fields-of-study/store', 'Admin\FieldOfStudyController::store');
    $routes->get('fields-of-study/edit/(:segment)', 'Admin\FieldOfStudyController::edit/$1');
    $routes->post('fields-of-study/update', 'Admin\FieldOfStudyController::update');
    $routes->get('fields-of-study/delete/(:segment)', 'Admin\FieldOfStudyController::delete/$1');

    // Ethnicities CRUD
    $routes->get('ethnicities', 'Admin\EthnicityController::index');
    $routes->get('ethnicities/create', 'Admin\EthnicityController::create');
    $routes->post('ethnicities/store', 'Admin\EthnicityController::store');
    $routes->get('ethnicities/edit/(:segment)', 'Admin\EthnicityController::edit/$1');
    $routes->post('ethnicities/update', 'Admin\EthnicityController::update');
    $routes->get('ethnicities/delete/(:segment)', 'Admin\EthnicityController::delete/$1');

    // Education Levels CRUD
    $routes->get('education-levels', 'Admin\EducationLevelController::index');
    $routes->get('education-levels/create', 'Admin\EducationLevelController::create');
    $routes->post('education-levels/store', 'Admin\EducationLevelController::store');
    $routes->get('education-levels/edit/(:segment)', 'Admin\EducationLevelController::edit/$1');
    $routes->post('education-levels/update', 'Admin\EducationLevelController::update');
    $routes->get('education-levels/delete/(:segment)', 'Admin\EducationLevelController::delete/$1');
    $routes->post('education-levels/reorder', 'Admin\EducationLevelController::reorder');


    // --------------------------------
    // 📌 JOBS CRUD (NEW)
    // --------------------------------
   
    $routes->get('jobs', 'Admin\JobsController::index');
    $routes->get('jobs/create', 'Admin\JobsController::create');
    $routes->post('jobs/store', 'Admin\JobsController::store');
    $routes->get('jobs/edit/(:segment)', 'Admin\JobsController::edit/$1');
    $routes->post('jobs/update/(:segment)', 'Admin\JobsController::update/$1'); 
    $routes->get('jobs/delete/(:segment)', 'Admin\JobsController::delete/$1');
    $routes->get('jobs/toggle/(:num)', 'Admin\JobsController::toggle/$1');
    $routes->get('jobs/(:segment)', 'Admin\JobsController::show/$1');


    $routes->get('jobs-applications', 'Admin\JobApplicationsController::index');

    // Show all applications for a specific job by UUID
    $routes->get('job-applications/(:segment)', 'Admin\JobApplicationsController::show/$1');

    $routes->post('job-application/(:num)/update-status', 'Admin\JobApplicationsController::updateStatus/$1', ['as' => 'admin.job-application.update-status']);



    
$routes->group('counties', function($routes) {
    $routes->get('', 'Admin\CountyController::index');                       
    $routes->match(['get', 'post'], 'add', 'Admin\CountyController::add');   
    $routes->match(['get', 'post'], 'edit/(:segment)', 'Admin\CountyController::edit/$1'); 
    $routes->get('delete/(:segment)', 'Admin\CountyController::delete/$1');  
});


    $routes->get('staffs', 'Admin\StaffController::index');
    

    $routes->get('applicants', 'Home::applicants');

   
    $routes->get('staffs/edit/(:any)?', 'Admin\StaffController::form/$1');
    $routes->post('staffs/save', 'Admin\StaffController::save');

   $routes->get('staffs/toggle-active/(:num)', 'Admin\StaffController::toggleActive/$1');


$routes->get('profile-review/(:segment)', 'ProfileController::resume/$1', ['as' => 'applicant.profile-review.user']);

$routes->get('get-user-by-email', 'Admin\StaffController::getUserByEmail');
$routes->get('admin-logs', 'Admin\AdminLogs::index');
$routes->get('user-logs', 'Admin\UserLogs::index');
$routes->get('mail-queue', 'Admin\MailQueueController::index', ['as' => 'admin.mailqueue']);

});



$routes->group('applicant', ['filter' => 'role:applicant'], function ($routes) {

    // Step 1: Basic Profile Details
    $routes->get('profile', 'ProfileController::index', ['as' => 'applicant.profile']);
    $routes->post('user-details/store', 'UserDetailsController::store', ['as' => 'applicant.user_details.store']);

    // Step 2: Professional Statement
    $routes->get('professional-statement', 'ProfileController::professionalStatement', ['as' => 'applicant.professional_statement']);
    $routes->post('professional-statement/store', 'ProfileController::saveProfessionalStatement', ['as' => 'applicant.professional_statement.store']);

    // Step 3: Education
    $routes->get('education', 'UserEducationController::index', ['as' => 'applicant.education']);
    $routes->get('education/create', 'UserEducationController::create', ['as' => 'applicant.education.create']);
    $routes->post('education/store', 'UserEducationController::store', ['as' => 'applicant.education.store']);
    $routes->get('education/edit/(:any)', 'UserEducationController::edit/$1', ['as' => 'applicant.education.edit']);
    $routes->post('education/update', 'UserEducationController::update', ['as' => 'applicant.education.update']);
    $routes->get('education/delete/(:any)', 'UserEducationController::delete/$1', ['as' => 'applicant.education.delete']);

    // Basic Education Routes
    $routes->get('basic-education', 'UserBasicEducationController::index', ['as' => 'applicant.basic_education']);
    $routes->get('basic-education/create', 'UserBasicEducationController::create', ['as' => 'applicant.basic_education.create']);
    $routes->post('basic-education/store', 'UserBasicEducationController::store', ['as' => 'applicant.basic_education.store']);
    $routes->get('basic-education/edit/(:any)', 'UserBasicEducationController::edit/$1', ['as' => 'applicant.basic_education.edit']);
    $routes->post('basic-education/update', 'UserBasicEducationController::update', ['as' => 'applicant.basic_education.update']);
    $routes->get('basic-education/delete/(:any)', 'UserBasicEducationController::delete/$1', ['as' => 'applicant.basic_education.delete']);

    // Higher Education Routes
    $routes->get('higher-education', 'UserHigherEducationController::index', ['as' => 'applicant.higher_education']);
    $routes->get('higher-education/create', 'UserHigherEducationController::create', ['as' => 'applicant.higher_education.create']);
    $routes->post('higher-education/store', 'UserHigherEducationController::store', ['as' => 'applicant.higher_education.store']);
    $routes->get('higher-education/edit/(:any)', 'UserHigherEducationController::edit/$1', ['as' => 'applicant.higher_education.edit']);
    $routes->post('higher-education/update', 'UserHigherEducationController::update', ['as' => 'applicant.higher_education.update']);
    $routes->get('higher-education/delete/(:any)', 'UserHigherEducationController::delete/$1', ['as' => 'applicant.higher_education.delete']);

    // Step 4: Memberships
    $routes->get('membership', 'UserMembershipController::index', ['as' => 'applicant.membership']);
    $routes->get('membership/create', 'UserMembershipController::create', ['as' => 'applicant.membership.create']);
    $routes->post('membership/store', 'UserMembershipController::store', ['as' => 'applicant.membership.store']);
    $routes->get('membership/edit/(:any)', 'UserMembershipController::edit/$1', ['as' => 'applicant.membership.edit']);
    $routes->post('membership/update', 'UserMembershipController::update', ['as' => 'applicant.membership.update']);
    $routes->get('membership/delete/(:any)', 'UserMembershipController::delete/$1', ['as' => 'applicant.membership.delete']);

    // Step 5: Certifications
    $routes->get('certification', 'UserCertificationController::index', ['as' => 'applicant.certification']);
    $routes->get('certification/create', 'UserCertificationController::create', ['as' => 'applicant.certification.create']);
    $routes->post('certification/store', 'UserCertificationController::store', ['as' => 'applicant.certification.store']);
    $routes->get('certification/edit/(:any)', 'UserCertificationController::edit/$1', ['as' => 'applicant.certification.edit']);
    $routes->post('certification/update', 'UserCertificationController::update', ['as' => 'applicant.certification.update']);
    $routes->get('certification/delete/(:any)', 'UserCertificationController::delete/$1', ['as' => 'applicant.certification.delete']);
    $routes->get('certification/by-body/(:num)', 'UserCertificationController::getCertificationsByBody/$1');

    // Step 6: Work Experience
    $routes->get('work-experience', 'UserWorkExperienceController::index', ['as' => 'applicant.work_experience']);
    $routes->get('work-experience/create', 'UserWorkExperienceController::create', ['as' => 'applicant.work_experience.create']);
    $routes->post('work-experience/store', 'UserWorkExperienceController::store', ['as' => 'applicant.work_experience.store']);
    $routes->get('work-experience/edit/(:any)', 'UserWorkExperienceController::edit/$1', ['as' => 'applicant.work_experience.edit']);
    $routes->post('work-experience/update', 'UserWorkExperienceController::update', ['as' => 'applicant.work_experience.update']);
    $routes->get('work-experience/delete/(:any)', 'UserWorkExperienceController::delete/$1', ['as' => 'applicant.work_experience.delete']);

    // Step 7: Referees
    $routes->get('referees', 'UserRefereesController::index', ['as' => 'applicant.referees']);
    $routes->get('referees/create', 'UserRefereesController::create', ['as' => 'applicant.referees.create']);
    $routes->post('referees/store', 'UserRefereesController::store', ['as' => 'applicant.referees.store']);
    $routes->get('referees/edit/(:any)', 'UserRefereesController::edit/$1', ['as' => 'applicant.referees.edit']);
    $routes->post('referees/update', 'UserRefereesController::update', ['as' => 'applicant.referees.update']);
    $routes->get('referees/delete/(:any)', 'UserRefereesController::delete/$1', ['as' => 'applicant.referees.delete']);


     $routes->get('applications', 'JobApplicationController::myApplications', ['as' => 'applicant.applications']);
    $routes->get('applications/apply/(:any)', 'JobApplicationController::apply/$1', ['as' => 'applicant.applications.apply']);
    $routes->get('applications/detail/(:any)', 'JobApplicationController::detail/$1', ['as' => 'applicant.applications.detail']);

 
    $routes->get('profile-review', 'ProfileController::resume', ['as' => 'applicant.profile-review']);
    

});
