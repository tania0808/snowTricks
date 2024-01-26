<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TricksFixtures extends Fixture implements DependentFixtureInterface
{
    private array $tricksList = [
        [
            'name' => 'Skating',
            'description' => 'Don’t rush up the slope just yet. Lock your leading foot into it’s binding, while keeping the back foot free. Stand on a flat surface and get a feel for the snowboard’s movement.',
            'category' => 'Basics',
            'image' => 'skating.jpg',
        ],
        [
            'name' => 'Heel slide',
            'description' => 'The first thing you need to learn on the slope is how to control the heel edge. Go find a flat-ish bunny hill — and let’s get to it.',
            'category' => 'Basics',
            'image' => 'heel_slide.jpg',
        ],
        [
            'name' => 'Turns',
            'description' => 'These are the turns you’ve been waiting to try, and probably see as the end game of learning to snowboard. This element consists of all the previous exercises. The only thing you need to add is a transition from board straight to toe slide.',
            'category' => 'Basics',
            'image' => 'turn.jpg',
        ],
        [
            'name' => 'Switch air',
            'description' => 'The switch air is mandatory. You’ll need it to tackle other tricks performed in switch. This is jump performed in one’s “unnatural” stance.',
            'category' => 'Jumps',
            'image' => 'switch_air.webp',
        ],
        [
            'name' => 'Ollie',
            'description' => 'An Ollie is probably the first snowboard trick you’ll learn. It’s your introduction to snowboard jumps. To perform an Ollie, you should shift your body weight to your back leg. Jump, making sure to lead with your front leg. Lift your back leg in line with your front. The more you practice the Ollie, the higher you can jump and the more parallel you can bring your feet.',
            'category' => 'Jumps',
            'image' => 'ollie.png',
        ],
        [
            'name' => 'Nollie',
            'description' => 'The Nollie is basically the opposite of an Ollie. When you jump, lead with your back leg. Then, lift your front leg to bring your feet in line with each other. You’ll probably find that you can catch a few inches after just a few tries.',
            'category' => 'Jumps',
            'image' => 'nollie.webp',
        ],
        [
            'name' => 'Frontside 180',
            'description' => 'Ready to rotate your snowboard? With a frontside 180, you rotate your body so that your heels lead. For example, if you jump while riding downhill with your left foot forward, you would rotate in a counter counterclockwise motion for a frontside 180. Stop when your rear leg becomes your leading leg.',
            'category' => 'Rotations',
            'image' => 'frontside_180.webp',
        ],
        [
            'name' => 'Butter',
            'description' => 'The butter takes a little more core strength than the frontside 180 and backside 180. Instead of bringing your back leg forward during a jump, you do it while maintaining contact with the snow. The snow creates a little more friction, so prepare to put some muscle into it.',
            'category' => 'Rotations',
            'image' => 'butter.jpg',
        ],
        [
            'name' => 'Butter Nose Rolls',
            'description' => 'Now that you can 180, we can start to combine that trick with butters! A butter nose roll is where you get into a nose butter, then combine that with a 180, so you are sliding sideways in the butter position while turning around to complete the 180. These can be done anywhere on the trail or over little hills!',
            'category' => 'Butters',
            'image' => 'butter_nose_rolls.jpeg',
        ],
        [
            'name' => '50/50 Grind',
            'description' => '50/50 grinds are the easiest way to get into the wonderful world of slides! You should learn these on wider boxes (often called butter boxes) in the park. They will have a mild slope to ride up to the box and aren’t too long.',
            'category' => 'Grinds',
            'image' => 'grind.jpg',
        ],
        [
            'name' => '50/50 Nose Press',
            'description' => 'This trick is done the same as the tail press, but with a nose butter across the box instead of a tail butter. These can be a bit trickier than tail presses, and the key is to really get all your weight over the front of your board. One thing to note is that you will need to get yourself back to center as you drop off the box. Otherwise, you will land in a nose press still and be off balance.',
            'category' => 'Presses',
            'image' => 'nose_press.webp',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < count($this->tricksList); ++$i) {
            $trick = new Trick();
            $trick->setName($this->tricksList[$i]['name']);
            $trick->setDescription($this->tricksList[$i]['description']);
            $trick->setCategory($this->getReference('category_'.strtolower($this->tricksList[$i]['category'])));
            $trick->setAuthor($this->getReference('user_'.$i));
            $media = new Media();
            $media->setName($this->tricksList[$i]['image']);
            $media->setType('image');
            $trick->addMedium($media);

            $manager->persist($trick);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoriesFixtures::class,
            UserFixtures::class,
        ];
    }
}
