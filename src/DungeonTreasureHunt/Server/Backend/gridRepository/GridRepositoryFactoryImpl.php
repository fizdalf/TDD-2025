<?php

namespace DungeonTreasureHunt\Backend\gridRepository;

class GridRepositoryFactoryImpl implements GridRepositoryFactory
{
    public function createForUser(string $username): GridFileSystemRepository
    {
        return new GridFileSystemRepository($username);
    }
}