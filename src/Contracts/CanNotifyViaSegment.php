<?php

namespace SlashEquip\LaravelSegment\Contracts;

interface CanNotifyViaSegment
{
    public function toSegment(CanBeIdentifiedForSegment $notifiable): CanBeSentToSegment;
}
