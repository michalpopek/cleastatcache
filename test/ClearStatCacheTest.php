<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

/**
 * A cold start is when you create a file object, modify its contents and fetch the size without any prior access.
 * A warm start is when you create the file, read its size, modify the contents, and then try to get the size
 * once more.
 */
class ClearStatCacheTest extends TestCase
{
    protected $path = __DIR__ . "/../fixtures/ClearStatCacheTest";

    public function testSplFileColdStart()
    {
        $file = $this->getSplFileInfo();

        $this->fillFile();
        $this->assertGreaterThan(0, $file->getSize());
        $this->assertGreaterThan(0, $this->getSplFileInfo()->getSize());
    }

    public function testSplFileWarmStart()
    {
        $file = $this->getSplFileInfo();
        $this->assertEquals(0, $file->getSize());

        $this->fillFile();
        $this->assertEquals(0, $file->getSize());
        $this->assertEquals(0, $this->getSplFileInfo()->getSize());

        $this->clearStatCache();
        $this->assertGreaterThan(0, $file->getSize());
        $this->assertGreaterThan(0, $this->getSplFileInfo()->getSize());
    }

    public function testSymfonyFileColdStart()
    {
        $file = $this->getSymfonyFile();

        $this->fillFile();
        $this->assertEquals(0, $file->getSize());
        $this->assertEquals(0, $this->getSymfonyFile()->getSize());

        $this->clearStatCache();
        $this->assertGreaterThan(0, $file->getSize());
        $this->assertGreaterThan(0, $this->getSymfonyFile()->getSize());
    }

    public function testSymfonyFileWarmStart()
    {
        $file = $this->getSymfonyFile();
        $this->assertEquals(0, $file->getSize());

        $this->fillFile();
        $this->assertEquals(0, $file->getSize());
        $this->assertEquals(0, $this->getSymfonyFile()->getSize());

        $this->clearStatCache();
        $this->assertGreaterThan(0, $file->getSize());
        $this->assertGreaterThan(0, $this->getSymfonyFile()->getSize());
    }

    protected function tearDown()
    {
        $this->clearFile();
        $this->clearStatCache();
    }

    protected function getSplFileInfo(): \SplFileInfo
    {
        return new \SplFileInfo($this->path);
    }

    protected function getSymfonyFile(): File
    {
        return new File($this->path);
    }

    protected function fillFile(): void
    {
        if (false === file_put_contents($this->path, "Test text")) {
            $this->fail("Could not fill file");
        }
    }

    protected function clearFile(): void
    {
        if (false === file_put_contents($this->path, null)) {
            $this->fail("Could not clear file");
        }
    }

    protected function clearStatCache(): void
    {
        clearstatcache(true, $this->path);
    }
}
