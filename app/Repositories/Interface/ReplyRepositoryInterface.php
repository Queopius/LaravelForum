<?php


namespace App\Repositories\Interface;

interface ReplyRepositoryInterface
{
    public function create($thread, $form);
    public function delete($reply);
    public function update($reply);
}
