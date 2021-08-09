<?php

namespace App\Events;

use App\Models\QuestionCopy;
use App\Models\ResultDetail;
use App\Models\ResultTest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmitQuestionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $resultTest;

    /**
     * Create a new event instance.
     *
     * @param ResultTest $resultTest
     */
    public function __construct(ResultTest $resultTest)
    {
        $this->resultTest = $resultTest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('result_teacher.'.$this->resultTest->id);
    }

    public function broadcastWith(): array
    {
        $resultDetail = $this->resultTest->resultDetails()->get();
        $resultDetail->transform(function ($record) {
            $record->student_choices = json_decode($record->student_choices, true);
            $record->student_choices = array_map(function ($r) {
                $id = array_key_first($r);
                $r["question_id"] = $id;
                $r["content"] = QuestionCopy::find($id)->title;
                $r["student_choice"] = $r[$id];
                unset($r[$id]);
                return $r;
            }, $record->student_choices);
            return $record;
        });
        return $resultDetail->toArray();
    }
}
