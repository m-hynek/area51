<?php declare(strict_types = 1);

namespace Area51;

use Area51\Api\Direction;
use Area51\Api\RobotApiClient;

class RobotExtraction
{
    public function __construct(
        private RobotApiClient $client
    ) {}

    public function extractRobot() : bool
    {
        $robotId = $this->client->create();

        $this->reachEdge($robotId, Direction::UP);
        $this->reachEdge($robotId, Direction::RIGHT);

        $dimensionY = $this->reachEdge($robotId, Direction::DOWN) + 1;
        $dimensionX = $this->reachEdge($robotId, Direction::LEFT) + 1;

        $this->move($robotId, Direction::UP, (int)floor($dimensionY / 2));
        $this->move($robotId, Direction::RIGHT, (int)floor($dimensionX / 2));

        return $this->client->escape($robotId) === true;
    }

    private function reachEdge(string $robotId, Direction $direction) : int
    {
        $distanceMoved = 0;

        while (true)
        {
            $moved = $this->client->move($robotId, $direction, Config::MAX_MOVE_DISTANCE);

            if ($moved === null)
            {
                continue;
            }

            $distanceMoved += $moved;

            if ($moved < Config::MAX_MOVE_DISTANCE)
            {
                break;
            }
        }

        return $distanceMoved;
    }

    private function move(string $robotId, Direction $direction, int $totalDistance)
    {
        $distanceMoved = 0;

        while (true)
        {
            $remaining = $totalDistance - $distanceMoved;

            if ($remaining === 0)
            {
                return;
            }

            if ($remaining < Config::MAX_MOVE_DISTANCE)
            {
                $moveDistance = $remaining;
            }
            else
            {
                $moveDistance = Config::MAX_MOVE_DISTANCE;
            }

            $moved = $this->client->move($robotId, $direction, $moveDistance);

            if ($moved === null)
            {
                continue;
            }

            $distanceMoved += $moved;
        }
    }
}