<?php

namespace aisol\resize;

use Imagick;

class Resize
{

    /**
     * Масштабирует изображение как можно больше c сохранением пропорций изображения (изображение не становится сплющенным).
     * Когда изображение и контейнер имеют разные размеры, изображение обрезается либо влево / вправо, либо сверху / снизу.
     */
    const COVER = 1;
    /**
     * Вписывает изображение в контейнер по высоте
     */
    const FIT_HEIGHT = 2;
    /**
     * Вписывает изображение в контейнер по ширине
     */
    const FIT_WIDTH = 3;
    /**
     * Масштабирует изображение так, чтобы оно максимально накрыло собой весь блок.
     * Изображение при этом не обрезается, а вписывается в блок с сохранением пропорций.
     */
    const FIT_AUTO = 4;
    /**
     * Масштабирует изображение так, чтобы оно максимально накрыло собой весь блок,
     * при этом размер изображения будет равен заданным параметрам, но будут добавлены рамки.
     * Изображение при этом не обрезается, а вписывается в блок с сохранением пропорций.
     */
    const CONTAIN_BOX = 5;

    private string $path;
    private Imagick $imagick;

    /**
     * @throws \ImagickException
     */
    public function __construct($path)
    {
        $this->path = realpath($path);
        $this->imagick = new Imagick($this->path);
    }

    /**
     * Масштабирует изображение как можно больше c сохранением пропорций изображения (изображение не становится сплющенным).
     * Когда изображение и контейнер имеют разные размеры, изображение обрезается либо влево / вправо, либо сверху / снизу.
     * @throws \ImagickException
     */
    public function cover(int $width, int $height): Resize
    {
        $this->imagick->cropThumbnailImage($width, $height);
        return $this;
    }

    /**
     * Вписывает изображение в контейнер по высоте
     * @throws \ImagickException
     */
    public function fitHeight(int $height): Resize
    {
        $this->imagick->scaleImage(0, $height);
        return $this;
    }

    /**
     * Вписывает изображение в контейнер по ширине
     * @throws \ImagickException
     */
    public function fitWidth(int $width): Resize
    {
        $this->imagick->scaleImage($width, 0);
        return $this;
    }

    /**
     * Вписывает изображение в контейнер по ширине или ширине в зависимости от размеров
     * @throws \ImagickException
     */
    public function fitAuto(int $width, int $height): Resize
    {
        $this->imagick->adaptiveResizeImage($width, $height, true);
        return $this;
    }

    /**
     * Вписывает изображение в контейнер по ширине или ширине в зависимости от размеров
     * @throws \ImagickException
     */
    public function containBox(int $width, int $height): Resize
    {
        $this->imagick->setGravity(Imagick::GRAVITY_CENTER);
        $this->imagick->adaptiveResizeImage($width, $height, true);
        $actualWidth = $this->imagick->getImageWidth();
        $actualHeight = $this->imagick->getImageHeight();
        $alpha = $this->imagick->getImageAlphaChannel();
        if ($alpha === Imagick::COLORSPACE_UNDEFINED) {
            $this->imagick->setImageBackgroundColor(new \ImagickPixel('white'));
        } else {
            $this->imagick->setImageBackgroundColor(new \ImagickPixel('transparent'));
        }
        $x = floor(($width / 2) - ($actualWidth / 2));
        $y = floor(($height / 2) - ($actualHeight / 2));
        $this->imagick->extentImage($width, $height, -$x, -$y);
        return $this;
    }

    /**
     * Поворачивает изображение по данным EXIF
     * @return $this
     * @throws \ImagickException
     */
    public function autoRotateImage(): Resize
    {
        $orientation = $this->imagick->getImageOrientation();
        switch($orientation) {
            case Imagick::ORIENTATION_BOTTOMRIGHT:
                $this->imagick->rotateImage('#000', 180);
                break;

            case Imagick::ORIENTATION_RIGHTTOP:
                $this->imagick->rotateImage('#000', 90);
                break;

            case Imagick::ORIENTATION_LEFTBOTTOM:
                $this->imagick->rotateImage('#000', -90);
                break;
        }
        $this->imagick->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
        return $this;
    }

    /**
     * Получение mime-type изображения
     * @throws \ImagickException
     */
    public function getMimeType(): string
    {
        return $this->imagick->getImageMimeType();
    }

    /**
     * Получение изображения для вывода в поток
     * @throws \ImagickException
     */
    public function toOutput(): string
    {
        return $this->imagick->getImageBlob();
    }

    /**
     * @throws \ImagickException
     */
    public function toFile(string $fileName): bool
    {
        return $this->imagick->writeImage($fileName);
    }

}
