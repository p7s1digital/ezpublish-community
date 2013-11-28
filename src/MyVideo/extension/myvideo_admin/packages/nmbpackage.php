<?php
/**
 * nmbPackage is extended eZPackage handler with bugfix for already known repo and packageName.
 *
 * @package
 * @author Max Keil <m.keil@all2e.com>
 * @date 02.07.12 13:10
 */
class nmbPackage extends eZPackage
{

    /**
     * Imports a package from a gzip compressed tarball file
     *
     * @param string $archiveName Path to the archive file
     * @param string $packageName Package name
     * @param bool $dbAvailable
     * @param bool|string $repositoryID
     *
     * @return \eZPackage|\nmbPackage The nmbPackage object if successfull, or one of the
     */
    public static function import($archiveName, &$packageName, $dbAvailable = true, $repositoryID = 'local')
    {
        if (is_dir($archiveName)) {
            eZDebug::writeError("Importing from directory is not supported.");
            $retValue = false;
            return $retValue;
        } else {
            $archiveOptions = new ezcArchiveOptions(array('readOnly' => true));
            $archive = ezcArchive::open("compress.zlib://$archiveName", ezcArchive::TAR_GNU, $archiveOptions);

            if (!self::isValidName($packageName)) {
                return self::STATUS_INVALID_NAME;
            }

            $existingPackage = self::fetch($packageName, false, false, $dbAvailable);
            if ($existingPackage) {
                return self::STATUS_ALREADY_EXISTS;
            }


            $fullRepositoryPath = self::repositoryPath() . '/' . $repositoryID;
            $packagePath = $fullRepositoryPath . '/' . $packageName;
            if (!file_exists($packagePath)) {
                eZDir::mkdir($packagePath, false, true);
            }
            $archive->extract($packagePath);

            $package = self::fetch($packageName, $fullRepositoryPath, false, $dbAvailable);
            if (!$package) {
                eZDebug::writeError("Failed loading imported package $packageName from $fullRepositoryPath");
            }

            return $package;
        }
    }

    /**
     * @static
     *
     * @param      $packageName
     * @param bool $packagePath
     * @param bool $repositoryID
     * @param bool $dbAvailable
     *
     * @return bool|eZPackage|nmbPackage
     */
    public static function fetch($packageName, $packagePath = false, $repositoryID = false, $dbAvailable = true)
    {
        $packageRepositories = self::packageRepositories(array('path' => $packagePath));

        if ($repositoryID === true) {
            $repositoryID = 'local';
        }

        foreach ($packageRepositories as $packageRepository) {
            if ($repositoryID !== false and
                $packageRepository['id'] != $repositoryID
            ) {
                continue;
            }
            $path = $packageRepository['path'];

            $path .= '/' . $packageName;
            $filePath = $path . '/' . self::definitionFilename();

            if (file_exists($filePath)) {
                $fileModification = filemtime($filePath);
                $package = false;
                $cacheExpired = false;

                if (self::useCache()) {
                    $package = self::fetchFromCache($path, $fileModification, $cacheExpired);
                }

                if ($package) {
                    $package->setCurrentRepositoryInformation($packageRepository);
                } else {
                    $package = self::fetchFromFile($filePath);

                    if ($package) {
                        $package->setCurrentRepositoryInformation($packageRepository);
                        if ($packagePath) {
                            $package->RepositoryPath = $packagePath;
                        }
                        if ($cacheExpired and
                            self::useCache()
                        ) {
                            $package->storeCache($path . '/' . self::cacheDirectory());
                        }
                    }
                }
                if ($dbAvailable) {
                    $package->getInstallState();
                }

                return $package;
            }
        }
        return false;
    }

    /**
     * Removes the package directory and all it's subfiles/directories.
     */
    public function remove()
    {
        $path = $this->path();
        if (file_exists($path)) {
            eZDir::recursiveDelete($path, false);
        }
        $this->setInstalled(false);
    }

    /**
     * @static
     *
     * @param $filename
     *
     * @return bool|eZPackage|nmbPackage
     */
    public static function fetchFromFile($filename)
    {
        $dom = self::fetchDOMFromFile($filename);

        if ($dom === false) {
            return false;
        }

        $package = new self();
        $parameters = $package->parseDOMTree($dom);
        if (!$parameters) {
            return false;
        }

        return $package;
    }

    /**
     * @static
     *
     * @param $packagePath
     * @param $packageModification
     * @param $cacheExpired
     *
     * @return bool|eZPackage|nmbPackage
     */
    public static function fetchFromCache($packagePath, $packageModification, &$cacheExpired)
    {
        $packageCachePath = $packagePath . '/' . self::cacheDirectory() . '/package.php';

        if (file_exists($packageCachePath)) {
            $cacheModification = filemtime($packageCachePath);
            if ($cacheModification >= $packageModification) {
                include($packageCachePath);
                if (!isset($CacheCodeDate) or
                    $CacheCodeDate != self::CACHE_CODE_DATE
                ) {
                    $cacheExpired = true;
                    return false;
                }
                if (isset($Parameters) and
                    isset($InstallData)
                ) {
                    $cacheExpired = false;
                    $package = new self($Parameters, false);
                    $package->InstallData = $InstallData;
                    return $package;
                }
            } else {
                $cacheExpired = true;
            }
        }
        $cacheExpired = true;
        return false;
    }
}
