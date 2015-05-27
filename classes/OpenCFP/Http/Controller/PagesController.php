<?php

namespace OpenCFP\Http\Controller;

class PagesController extends BaseController
{
    public function showHomepage()
    {
        return $this->render('home.twig', $this->getContextWithTalksCount());
    }

    public function showSpeakerPackage()
    {
        return $this->render('package.twig', $this->getContextWithTalksCount());
    }

    public function showTalkIdeas()
    {
        return $this->render('ideas.twig', $this->getContextWithTalksCount());
    }

    private function getContextWithTalksCount()
    {
        $numberOfTalks = $this->app['spot']->mapper('OpenCFP\Domain\Entity\Talk')->all()->count();

        return [
            'number_of_talks' => $numberOfTalks
        ];
    }
}
