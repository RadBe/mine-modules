<?php


namespace App\TopVotes\Tops;


class MinecraftRating extends MonitoringMinecraft
{
    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'minecraftrating';
    }
}
