<?php

namespace App\Models;

use App\Traits\UuidTrait;
use CodeIgniter\Model;
use RuntimeException;
use Tatter\Relations\Traits\ModelTrait;

class User extends Model
{
    use UuidTrait;
    use ModelTrait;

    protected $table            = 'users';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields = false;

    // Dates
    protected $useTimestamps = true;

    protected $with = ['roles'];

    protected $allowedFields = [
        'id',
        'role_id',
        'names',
        'avatar',
        'avatar',
        'telephone_number',
        'country',
        'email',
        'password',
        'status',
    ];
    protected $updatedField = "updated_at";
    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    public const STATUS = [
        'ACTIVE', 'DISABLED',
    ];
    public const ACTIVE = self::STATUS[0];
    public const DISABLED = self::STATUS[1];

    public function getTable()
    {
        return $this->table;
    }

    public function insert($data = null, bool $returnID = true)
    {
        if (! isset($data['id'])) {
            $data['id'] = $this->generateUuid();
        }

        return parent::insert($data, $returnID);
    }

    protected function beforeInsert(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    protected function beforeUpdate(array $data): array
    {
        return $this->getUpdatedDataWithHashedPassword($data);
    }

    private function getUpdatedDataWithHashedPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $plaintextPassword = $data['data']['password'];
            $data['data']['password'] = $this->hashPassword($plaintextPassword);
        }
        return $data;
    }

    private function hashPassword(string $plaintextPassword): string
    {
        return password_hash($plaintextPassword, PASSWORD_ARGON2I);
    }

    public function findUserByEmailAddress(string $emailAddress)
    {
        $user = $this->asArray()
            ->where(['email' => $emailAddress])
            ->first();

        if (!$user) {
            throw new RuntimeException('User does not exist for specified email address');
        }

        return $user;
    }

    /**
     * Finds a user by their ID.
     *
     * @param string $id The ID of the user.
     *
     * @return array|object Returns the user data as an array or object.
     *
     * @throws RuntimeException If the user does not exist for the specified ID.
     */
    public function findUserById(string $id){
        $user = $this->asArray()
            ->where(['id' => $id])
            ->first();

        if (!$user) {
            throw new RuntimeException('User does not exist!');
        }
        return $user;
    }

}
