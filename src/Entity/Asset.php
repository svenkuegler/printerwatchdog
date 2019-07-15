<?php

namespace App\Entity;

class Asset
{
    /**
     * @var int
     */
    private $assetId;

    /**
     * @var string
     */
    private $assetName;

    /**
     * @var string
     */
    private $assetTag;

    /**
     * @return int
     */
    public function getAssetId(): int
    {
        return $this->assetId;
    }

    /**
     * @param int $assetId
     * @return Asset
     */
    public function setAssetId(int $assetId): Asset
    {
        $this->assetId = $assetId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAssetName(): string
    {
        return $this->assetName;
    }

    /**
     * @param string $assetName
     * @return Asset
     */
    public function setAssetName(string $assetName): Asset
    {
        $this->assetName = $assetName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAssetTag(): string
    {
        return $this->assetTag;
    }

    /**
     * @param string $assetTag
     * @return Asset
     */
    public function setAssetTag(string $assetTag): Asset
    {
        $this->assetTag = $assetTag;
        return $this;
    }


}