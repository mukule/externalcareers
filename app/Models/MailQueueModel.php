<?php

namespace App\Models;

use CodeIgniter\Model;

class MailQueueModel extends Model
{
    protected $table            = 'mail_queue';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Fields that can be set during insert/update
    protected $allowedFields = [
        'to_email',
        'subject',
        'body',
        'status',
        'attempts',
        'error_message',
        'created_at',
        'sent_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // We don't have an updated_at in your desc, so leave blank

    /**
     * Get pending emails that haven't exceeded max attempts
     */
    public function getPending(int $limit = 10)
    {
        return $this->whereIn('status', ['pending', 'failed'])
                    ->where('attempts <', 5)
                    ->orderBy('created_at', 'ASC')
                    ->findAll($limit);
    }

    /**
     * Mark an email as currently being processed
     */
    public function markAsProcessing(int $id)
    {
        return $this->update($id, ['status' => 'processing']);
    }

    /**
     * Update status after a successful send
     */
    public function markAsSent(int $id)
    {
        return $this->update($id, [
            'status'  => 'sent',
            'sent_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Log a failure and increment attempt count
     */
    public function markAsFailed(int $id, string $errorMessage)
    {
        $current = $this->find($id);
        return $this->update($id, [
            'status'        => 'failed',
            'attempts'      => ($current['attempts'] ?? 0) + 1,
            'error_message' => $errorMessage
        ]);
    }


    public function getFiltered(array $filters = [], int $perPage = 20, int $page = 1)
{
    $builder = $this->builder();

    // Apply status filter
    if (!empty($filters['status'])) {
        $builder->where('status', $filters['status']);
    }

    // Apply recipient email filter (partial match)
    if (!empty($filters['to_email'])) {
        $builder->like('to_email', $filters['to_email']);
    }

    // Apply subject filter (partial match)
    if (!empty($filters['subject'])) {
        $builder->like('subject', $filters['subject']);
    }

    // Filter by created_at date range
    if (!empty($filters['date_from'])) {
        $builder->where('created_at >=', $filters['date_from'] . ' 00:00:00');
    }
    if (!empty($filters['date_to'])) {
        $builder->where('created_at <=', $filters['date_to'] . ' 23:59:59');
    }

    // Pagination
    $builder->orderBy('created_at', 'DESC');
    $offset = ($page - 1) * $perPage;
    $data = $builder->get($perPage, $offset)->getResultArray();

    // Total count for pagination
    $total = $builder->countAllResults(false); // false to not reset builder

    return [
        'data'       => $data,
        'total'      => $total,
        'perPage'    => $perPage,
        'currentPage'=> $page,
        'totalPages' => ceil($total / $perPage),
    ];
}


}