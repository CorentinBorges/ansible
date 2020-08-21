<?php

namespace App\Repository;

use App\Entity\Figure;
use App\Entity\Image;
use App\Entity\Video;
use App\Form\TrickFormType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends BaseRepository
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, Filesystem $filesystem)
    {
        parent::__construct($registry, Image::class,$entityManager);
        $this->filesystem = $filesystem;
    }



    public function createImage(UploadedFile $uploadedFile,$formImageName,$figure)
    {

        $image = new Image();
        $imageName = uniqid() . $formImageName;
        $image->setName($imageName)
            ->setFigure($figure)
            ->setFirst('false');
        $this->entityManager->persist($image);
        $this->entityManager->flush();
        $uploadedFile->move('images/tricks/', $imageName);
    }

    public function findFirst($figureId)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.figure = :val')
            ->andWhere('i.first = true')
            ->setParameter('val', $figureId)
            ->getQuery()
            ->getOneOrNullResult();

    }

    /*public function createImages(FormInterface $form, Figure $figure)
    {
        for ($i=1;$i<=TrickFormType::NB_IMAGE;$i++) {
            if (isset($form['image' . $i]) && !empty($form['image' . $i]->getData())) {
                $image = new Image();
                $imageName = uniqid() . $form['image' . $i]->getData()->getClientOriginalName();
                $image
                    ->setName($imageName)
                    ->setFigure($figure)
                    ->setFirst($i == 1);

                $this->entityManager->persist($image);
                @var  $file File
                $file = $form['image' . $i]->getData();
                $file->move('images/tricks/', $imageName);
            }
        }
    }*/

    public function editImage(Figure $figure,Image $newImage)
    {
        $image = $this->findOneBy(["id" => $newImage->getId()]);
        if ($newImage->getFirst()) {
            if ($oldImgFirst=$this->findFirst($figure->getId())) {
                /** @var Image $oldImgFirst */
                $oldImgFirst->setFirst(false);
            }
        }
        if ($image->getFirst() && ($newImage->getFirst()===false)) {
            $image->setFirst(false);
        }
        $image->setFirst($newImage->getFirst());
        $image->setName($newImage->getName());
        $image->setAlt($newImage->getAlt());
        $this->entityManager->persist($image);
        $this->entityManager->flush();
    }

    public function deletePicsFromTrick($trickId,Filesystem $filesystem)
    {
        $tricksPics = $this->findBy(["figure" => $trickId]);
        foreach ($tricksPics as $trickPic) {
            $filesystem->remove('images/tricks/'.$trickPic->getName());
            $this->entityManager->remove($trickPic);
        }
    }

    public function deletePic($id,Filesystem $filesystem)
    {
        $pic = $this->findOneBy(["id" => $id,]);
        $filesystem->remove('images/tricks/'.$pic->getName());
        $this->deleteFromDatabase($pic);
    }
    // /**
    //  * @return Image[] Returns an array of Image objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Image
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
