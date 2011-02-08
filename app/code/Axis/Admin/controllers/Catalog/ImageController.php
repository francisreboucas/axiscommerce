<?php
/**
 * Axis
 *
 * This file is part of Axis.
 *
 * Axis is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Axis is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Axis.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category    Axis
 * @package     Axis_Admin
 * @subpackage  Axis_Admin_Controller
 * @copyright   Copyright 2008-2010 Axis
 * @license     GNU Public License V3.0
 */

/**
 *
 * @category    Axis
 * @package     Axis_Admin
 * @subpackage  Axis_Admin_Controller
 * @author      Axis Core Team <core@axiscommerce.com>
 */
class Axis_Admin_Catalog_ImageController extends Axis_Admin_Controller_Back
{
    /**
     * @param string $path
     * @return string|false
     */
    private function _getPath($path)
    {
        $root = Axis::config()->system->path . '/media';

        $path = trim($path, '/\\');
        $path = Axis::config()->system->path . '/' . $path;

        $path = str_replace('\\', '/', realpath($path));
        if (file_exists($path) && strpos($path, $root) === 0) {
            return $path;
        }
        return false;
    }

    /**
     * @param string $path
     * @return string
     */
    private function _getIcon($path)
    {
        if (is_dir($path)) {
            return 'folder';
        } else {
            if (false !== strrpos($path, '.')) {
                return 'file-' . substr($path, strrpos($path, '.') + 1);
            }
        }
        return 'file';
    }

    /**
     * @param string $path
     * @param string $mode ['all', 'file', 'folder']
     * @param bool $recursive
     * @param array $items [optional]
     * @return array
     */
    private function _scanFolder($path, $mode, $recursive, $items = array())
    {
        if (!is_dir($path)) {
            return $items;
        }
        try {
            $di = new DirectoryIterator($path);
        } catch (Exception $e) {
            throw new Axis_Exception(
                Axis::translate('core')->__(
                    "Directory %s not readable", $path
                )
            );
        }

        foreach ($di as $f) {
            $s = $f->getFilename();
            if ($s[0] == '.') {
                continue;
            }

            $path = str_replace('\\', '/', $f->getPathname());

            $isDir = $f->isDir();

            if (($isDir && $mode != 'file') || (!$isDir && $mode != 'folder')) {
                $item = array(
                    'text'          => $f->getBasename(),
                    'absolute_path' => $path,
                    'path'          => str_replace(Axis::config('system/path') . '/', '', $path),
                    'absolute_url'  => $this->view->href(
                            str_replace(Axis::config('system/path'), '', $path),
                            true,
                            false
                        ),
                    'iconCls'       => $this->_getIcon($path),
                    'leaf'          => !$isDir,
                    'is_dir'        => $isDir
                );
                if (!$isDir) {
                    $item['qtip'] = 'Size: ' . $f->getSize();
                }
                $items[] = $item;
            }

            if ($isDir && $recursive) {
                $items += $this->_scanFolder($path, $mode, $recursive, $items);
            }
        }

        return $items;
    }

    /**
     * @param string $path
     * @return bool
     */
    private function _delete($path)
    {
        /* check are we in 'ROOT/media' */
        if (false === strpos($path, Axis::config()->system->path . '/media')) {
            return false;
        }

        if (is_dir($path)) {
            $path = rtrim($path, '/');
            $dir = dir($path);
            while (false !== ($file = $dir->read())) {
                if ($file != '.' && $file != '..') {
                    (!is_link("$path/$file") && is_dir("$path/$file")) ?
                        $this->_delete("$path/$file") : unlink("$path/$file");
                }
            }
            $dir->close();
            rmdir($path);
            return true;
        } else {
            unlink($path);
        }

        return false;
    }

    private function _getAction()
    {
        $result = array();
        if ($path = $this->_getPath($this->_getParam('path'))) {
            $result = $this->_scanFolder(
                $path,
                $this->_getParam('mode', 'all'),
                (bool) $this->_getParam('recursive', false)
            );
        }

        return $this->_helper->json->sendRaw($result);
    }

    private function _uploadAction()
    {
        $this->_helper->layout->disableLayout();

        $result = array();
        foreach ($_FILES as $key => $values) {
            if (strpos($key, 'ext-gen') !== 0) {
                continue;
            }
            try {
                $uploader = new Axis_File_Uploader($key);
                $file = $uploader
                    ->setUseDispersion(false)
                    ->save(Axis::config()->system->path . '/' . $this->_getParam('path'));

                $result = array(
                    'success' => true,
                    'data' => array(
                        'path' => $file['path'],
                        'file' => $file['file']
                    )
                );
            } catch (Axis_Exception $e) {
                $result = array(
                    'success' => false,
                    'messages' => array(
                        'error' => $e->getMessage()
                    )
                );
            }
        }

        return $this->getResponse()->appendBody(Zend_Json_Encoder::encode($result));
    }

    public function _deleteAction()
    {
        if ($this->_getParam('batch', 0)) {
            foreach (Zend_Json::decode($this->_getParam('file')) as $path) {
                $this->_delete(Axis::config()->system->path . '/' . $path);
            }
            return $this->_helper->json->sendSuccess();
        }

        if (!$this->_delete(Axis::config()->system->path . '/' . $this->_getParam('file'))) {
            return $this->_helper->json->sendFailure();
        }

        return $this->_helper->json->sendSuccess();
    }

    public function _newdirAction()
    {
        if (!@mkdir(Axis::config()->system->path . '/' . $this->_getParam('dir'), 0777, true)) {
            return $this->_helper->json->sendFailure();
        }

        return $this->_helper->json->sendSuccess();
    }

    public function _renameAction()
    {
        if (!@rename(
                Axis::config()->system->path . '/' . $this->_getParam('oldname'),
                Axis::config()->system->path . '/' . $this->_getParam('newname')
           )) {

           return $this->_helper->json->sendFailure();
       }

       return $this->_helper->json->sendSuccess();
    }

    public function treePanelAction()
    {
        $this->_helper->layout->disableLayout();

        $cmd = $this->_getParam('cmd');
        $method = '_' . $cmd . 'Action';

        if (!method_exists($this, $method)) {
            Axis::message()->addError(Axis::translate('catalog')->__(
                'Method %s not exist', $method
            ));
            return $this->getResponse()->appendBody(Zend_Json_Encoder::encode(array(
                'success' => false,
                'messages' => Axis::message()->get()
            )));
        }

        return $this->$method();
    }
}