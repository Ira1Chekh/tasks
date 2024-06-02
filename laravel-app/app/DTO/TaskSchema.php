<?php

namespace App\DTO;

/**
 * @OA\Schema(
 *     schema="Task",
 *     type="object",
 *     title="Task",
 *     description="Task model",
 *     required={"title", "status", "user_id"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the task"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the task"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the task"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "in_progress", "completed"},
 *         description="Status of the task"
 *     ),
 *      @OA\Property(
 *         property="priority",
 *         type="integer",
 *         description="Priority of the task"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID of the user who owns the task"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp"
 *     )
 * )
 */
class TaskSchema {}