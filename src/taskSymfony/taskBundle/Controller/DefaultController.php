<?php

namespace taskSymfony\taskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\BrowserKit\Request;
use taskSymfony\taskBundle\Entity\Product;


class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $product = new Product();
        // Генерируем форму для добавления нового продукта
        $formProduct = $this->createFormBuilder($product)
            ->add('title', 'text', array('label' => 'Название'))
            ->add('description', 'textarea', array('label' => 'Описание'))
            ->add('photo', 'file', array('label' => 'Фото'))
            ->add('save', 'submit', array('label' => 'Добавить'))
            ->getForm();

        $request = $this->get('request');
        $formProduct->handleRequest($request);
        // Проверяем на валидность
        if ($formProduct->isValid()) {
            // В тестовом сделал возможность загрузки только jpgов
            if ($formProduct->getData()->getPhoto()->getClientOriginalExtension() !== 'jpg'
                && $formProduct->getData()->getPhoto()->getClientOriginalExtension() !== 'jpeg') {
                echo '<h3>Файл имеет некорректный тип. Попробуйте загрузить jpg формат.</h3>';
            } else {
                $photoName = md5(time());
                $formProduct->getData()->getPhoto()->move('images/', $photoName . '.' .
                    $formProduct->getData()->getPhoto()->getClientOriginalExtension());


                $product->setTitle($formProduct->getData()->getTitle());
                $product->setDescription($formProduct->getData()->getDescription());
                $product->setPhoto($photoName . '.' . $formProduct->getData()->getPhoto()->getClientOriginalExtension());

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($product);
                $em->flush();
                echo '<h1>Продукт успешно добавлен</h1>';
            }
        }
        $products = $this->getDoctrine()
            ->getRepository('taskSymfonytaskBundle:Product')
            ->findAll();

        return array(
            'form' => $formProduct->createView(),
            'products' => $products
        );
    }

    /**
     * @Route("/remove/{id}")
     * @param int $id идентификатор продукта
     * @return redirect
     */
    public function removeItemAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        // Получаем нужный объект продукта по idшнику и удаляем его
        $product = $em
            ->getRepository('taskSymfonytaskBundle:Product')
            ->findOneBy(array('id' => $id));
        $em->remove($product);
        $em->flush();
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @Route("/update")
     */
    public function updateItemAction()
    {

    }
}
