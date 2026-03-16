<?php

use App\Models\UserModel;
use App\Models\MemberDetailModel;
use App\Models\NextOfKinModel;
use App\Models\BankAccountModel;

if (!function_exists('checkMemberReadiness')) {
  
    function checkMemberReadiness(int $userId): array
    {
        $missing = [];
        $sections = [
            'profile' => false,
            'next_of_kin' => false,
            'bank_account' => false,
        ];

       
        $userModel = new UserModel();
        $detailModel = new MemberDetailModel();

        $user = $userModel->find($userId);
        $details = $detailModel->getByUserId($userId);

        $requiredUserFields = ['first_name', 'last_name', 'email', 'phone'];
        $requiredDetailFields = ['gender', 'date_of_birth', 'id_no', 'county', 'address', 'occupation', 'marital_status'];

        $profileMissing = [];

        foreach ($requiredUserFields as $field) {
            if (empty($user[$field])) {
                $profileMissing[] = ucfirst(str_replace('_', ' ', $field));
            }
        }

        foreach ($requiredDetailFields as $field) {
            if (empty($details[$field])) {
                $profileMissing[] = ucfirst(str_replace('_', ' ', $field));
            }
        }

        if (($details['employment_term'] ?? '') === 'Contract' && empty($details['contract_expiry_date'])) {
            $profileMissing[] = 'Contract Expiry Date';
        }

        if (empty($profileMissing)) {
            $sections['profile'] = true;
        } else {
            $missing = array_merge($missing, $profileMissing);
        }

        
        $nextOfKinModel = new NextOfKinModel();
        $kins = $nextOfKinModel->getByUserId($userId);

        if (count($kins) > 0) {
            $sections['next_of_kin'] = true;
        } else {
            $missing[] = 'Next of Kin details';
        }

        
        $bankModel = new BankAccountModel();
        $bank = $bankModel->where('user_id', $userId)->where('active', 1)->first();

        if ($bank) {
            $sections['bank_account'] = true;
        } else {
            $missing[] = 'Active Bank Account';
        }

        
        $totalSections = count($sections);
        $completedSections = count(array_filter($sections));
        $percentage = round(($completedSections / $totalSections) * 100);

        return [
            'ready' => empty($missing),
            'missing' => $missing,
            'percentage' => $percentage,
            'sections' => $sections,
        ];
    }
}
