<?php

/**
 * Tests for our TalkForm object
 */

class TalkFormTest extends \PHPUnit_Framework_TestCase
{
    private $purifier;

    protected function setUp()
    {
        $this->purifier = new \HTMLPurifier();
    }

    /**
     * Test that form object correctly detects if all the required fields
     * are in the user-submitted data
     *
     * @test
     * @dataProvider hasRequiredProvider
     * @param array   $rawData  serialized user-submitted data
     * @param boolean $response
     */
    public function correctlyDetectsRequiredFields($rawData, $response)
    {
        $data = unserialize($rawData);
        $form = new \OpenCFP\Http\Form\TalkForm($data, $this->purifier);

        $this->assertEquals(
            $response,
            $form->hasRequiredFields(),
            '\OpenCFP\Form\TalkForm::hasRequired() did not work correctly'
        );
    }

    /**
     * Data provider correctlyDetectsRequiredFields
     *
     * @return array
     */
    public function hasRequiredProvider()
    {
        $badData = array(
            'title' => 'Bad Data',
            'description' => 'Hey, why are we missing fields!'
        );
        $goodData = array(
            'title' => 'Talk Title',
            'description' => 'Description of our talk',
            'type' => 'session',
            'category' => 'development',
            'level' => 'entry',
            'slides' => 'http://slideshare.net',
            'other' => 'Misc comments',
            'desired' => 1,
            'sponsor' => 1,
            'user_id' => 1
        );
        $extendedData = $goodData;
        $extendedData['extra'] = "Extra data in \$_POST but we ignore it";

        return array(
            array(serialize($badData), false),
            array(serialize($goodData), true),
            array(serialize($extendedData), true)
        );
    }

    /**
     * Test that title data is properly validated
     *
     * @test
     * @dataProvider titleValidatesProvider
     * @param string  $title
     * @param boolean $expectedResponse
     */
    public function titleValidatesCorrectly($title, $expectedResponse)
    {
        $data = array('title' => $title);
        $form = new \OpenCFP\Http\Form\TalkForm($data, $this->purifier);
        $form->sanitize();

        $this->assertEquals(
            $expectedResponse,
            $form->validateTitle(),
            '\OpenCFP\Form\TalkForm::validateTitle() did not apply validation rules correctly'
        );
    }

    /**
     * Data provider for titleValidatesCorrectly
     *
     * @return array
     */
    public function titleValidatesProvider()
    {
        $faker = \Faker\Factory::create();

        return array(
            array(substr($faker->text(90), 0, 90), true),
            array(null, false),
            array("This is a string that could be more than 100 characters long but will we really know for sure until I check it out?", false),
            array("A little bit of this & that", true)
        );
    }

    /**
     * Test that description data is being properly validated
     *
     * @test
     * @dataProvider descriptionValidatesProvider
     * @param string  $description
     * @param boolean $expectedResponse
     */
    public function descriptionValidatesCorrectly($description, $expectedResponse)
    {
        $data = array('description' => $description);
        $form = new \OpenCFP\Http\Form\TalkForm($data, $this->purifier);
        $form->sanitize();

        $this->assertEquals(
            $expectedResponse,
            $form->validateDescription(),
            '\OpenCFP\Form\TalkForm::validateDescription() did not apply validation rules correctly'
        );
    }

    /**
     * Data provider for descriptionValidatesCorrectly
     *
     * @return array
     */
    public function descriptionValidatesProvider()
    {
        $faker = \Faker\Factory::create();

        return array(
            array($faker->text(), true),
            array(null, false),
            array('<script>alert("XSS");</script>', false),
        );
    }

    /**
     * Test that validates the talk type
     *
     * @test
     * @dataProvider typeProvider
     * @param string  $type
     * @param boolean $expectedResponse
     */
    public function typeValidatesCorrectly($type, $expectedResponse)
    {
        $data = array('type' => $type);
        $form = new \OpenCFP\Http\Form\TalkForm($data, $this->purifier);
        $form->sanitize();

        $this->assertEquals(
            $expectedResponse,
            $form->validateType(),
            '\OpenCFP\Form\TalkForm::validateType() did not apply validation rules correctly'
        );
    }

    /**
     * Data provider for typeValidatesCorrectly
     *
     * @return boolean
     */
    public function typeProvider()
    {
        return array(
            array('regular', true),
            array('tutorial', true),
            array('foo', false),
            array(null, false),
            array(false, false),
            array(1, false),
            array(true, false)
        );
    }

    /**
     * Data provider for speakerIdValidates
     *
     * @return array
     */
    public function speakerIdProvider()
    {
        $validSpeakerInfo = array(
            'user_id' => 1,
            'info' => 'Special speaker info'
        );

        return array(
            array(1, $validSpeakerInfo, true),
            array(0, false, false),
            array(null, false, false),
            array(true, false, false),
            array(false, false, false),
            array('user', false, false)
        );
    }
}
