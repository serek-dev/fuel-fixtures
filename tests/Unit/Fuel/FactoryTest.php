<?php

declare(strict_types=1);

namespace Tests\Unit\Fuel;

use Orm\Model;
use PHPUnit\Framework\TestCase;
use Stwarog\FuelFixtures\Exceptions\OutOfStateBound;
use Stwarog\FuelFixtures\Fuel\Factory;
use Stwarog\FuelFixtures\Fuel\FactoryContract;
use Stwarog\FuelFixtures\Fuel\FuelPersistence;
use Stwarog\FuelFixtures\Fuel\PersistenceContract;
use Stwarog\FuelFixtures\State;
use Tests\Unit\Mocks\ModelImitation;

/** @covers \Stwarog\FuelFixtures\Fuel\Factory */
final class FactoryTest extends TestCase
{
    /** @test */
    public function constructor(): void
    {
        // When initialized
        // Given factory
        $actual = $this->getFactory();

        // Then it should implement FactoryContract
        $this->assertInstanceOf(FactoryContract::class, $actual);
        $this->assertNotNull($actual->getPersistence());
        $this->assertNotNull($actual->getFaker());
    }

    /** @test */
    public function constructor_WithGivenPersistenceAndFaker_ShouldContainsGivenInstances(): void
    {
        // When initialized
        // Given factory
        $factory = $this->getFactory();
        $factoryClass = get_class($factory);

        // And persistence
        $persistence = new FuelPersistence();

        // And faker instance
        $faker = \Faker\Factory::create();

        // When factory created
        $newFactory = new $factoryClass($persistence, $faker);

        // Then new class should have the some persistence and faker
        $this->assertSame($faker, $newFactory->getFaker());
        $this->assertSame($persistence, $newFactory->getPersistence());
    }

    /** @test */
    public function from_Factory_ShouldCreateWithGivenPersistenceAndFaker(): void
    {
        // Given factory
        $relatedFactory = $this->createMock(FactoryContract::class);

        // And persistence
        $persistence = new FuelPersistence();

        $relatedFactory->expects($this->once())
            ->method('getPersistence')
            ->willReturn($persistence);

        // And faker instance
        $faker = \Faker\Factory::create();

        $relatedFactory->expects($this->once())
            ->method('getFaker')
            ->willReturn($faker);

        // When factory create from related factory
        $factory = $this->getFactory();
        $factoryClass = get_class($factory);

        $newFactory = $factoryClass::from($relatedFactory);

        // Then new class should have the some persistence and faker
        $this->assertSame($faker, $newFactory->getFaker());
        $this->assertSame($persistence, $newFactory->getPersistence());
    }

    public function getFactory(): Factory
    {
        return new class extends Factory {
            public function getDefaults(): array
            {
                return [
                    'id' => 'id',
                    'status' => 'status',
                    'body' => 'body',
                    'relation' => null
                ];
            }

            public static function getClass(): string
            {
                return ModelImitation::class;
            }

            /** @inheritDoc */
            public function getStates(): array
            {
                return [
                    'fake' => static function (ModelImitation $model, array $attributes = []) {
                        $model->body = 'fake';
                    },
                    'factory' => ['relation', $this]
                ];
            }
        };
    }

    /** @test */
    public function makeOne_NoAttributes_ShouldCreateWithDefaults(): Model
    {
        // Given factory
        $factory = $this->getFactory();
        $expected = $factory->getDefaults();

        // When makeOne is called
        $model = $factory->makeOne();
        $actual = $model->to_array();

        // Then defaults should be assigned
        $this->assertSame($expected, $actual);

        return $model;
    }

    /** @test */
    public function invoke_NoAttributes_ShouldActAsMakeOne(): void
    {
        // Given factory
        $factory = $this->getFactory();
        $expected = $factory->getDefaults();

        // When invoke is called
        $model = $factory();
        $actual = $model->to_array();

        // Then defaults should be assigned
        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @depends makeOne_NoAttributes_ShouldCreateWithDefaults
     */
    public function makeOne_CreatedModelMustBeInstanceOfModel(object $model): void
    {
        $this->assertInstanceOf(Model::class, $model);
    }

    /** @test */
    public function makeOne_AttributesGiven_ShouldMergeWithDefaults(): Model
    {
        // Given factory
        $factory = $this->getFactory();
        $factory->getDefaults();
        $attributes = ['id' => 123, 'field' => 'field'];
        $expected = [
            'id' => 123,
            'status' => 'status',
            'body' => 'body',
            'relation' => null,
            'field' => 'field',
        ];

        // When makeOne is called
        $model = $factory->makeOne($attributes);
        $actual = $model->to_array();

        // Then defaults should be overwritten by given attributes
        $this->assertSame($expected, $actual);

        return $model;
    }

    /**
     * @test
     * @return array{FactoryContract, array<Model>}
     */
    public function makeMany_NoAttributes_ShouldCreateWithDefaults(): array
    {
        // Given factory
        $factory = $this->getFactory();
        $expected = $factory->getDefaults();

        // When makeMany is called
        $models = $factory->makeMany();

        foreach ($models as $model) {
            $actual = $model->to_array();

            // Then defaults should be assigned to all created models
            $this->assertSame($expected, $actual);
        }

        return [$factory, $models];
    }

    /**
     * @test
     * @param array{FactoryContract, array<Model>} $data
     * @depends makeMany_NoAttributes_ShouldCreateWithDefaults
     */
    public function makeMany_NoCountPassed_ShouldCreateFiveModels(array $data): void
    {
        // Given models generated without count param
        [, $models] = $data;
        $expected = 5;
        // Then count should be = 5
        $this->assertCount($expected, $models);
    }

    /**
     * @test
     * @param array{FactoryContract, array<Model>} $data
     * @depends makeMany_NoAttributes_ShouldCreateWithDefaults
     */
    public function makeMany_eachMustBeInstanceOfModel(array $data): void
    {
        // Given models generated by makeMany
        [, $models] = $data;

        // Then each model must be
        foreach ($models as $model) {
            $this->assertInstanceOf(Model::class, $model);
        }
    }

    /**
     * @test
     * @param array{FactoryContract, array<Model>} $data
     * @depends makeMany_NoAttributes_ShouldCreateWithDefaults
     */
    public function makeMany_CountPassed_ShouldCreateGivenCountOfModels(array $data): void
    {
        // Given models generated without count param
        [$factory] = $data;
        $expected = 2;

        // When created
        $models = $factory->makeMany($attributes = [], $expected);

        // Then count should be = 2
        $this->assertCount($expected, $models);
    }

    /** @test */
    public function createOne_NoAttributes_ShouldCreateWithDefaults(): Model
    {
        // Given factory
        $factory = $this->getFactory();
        $expected = $factory->getDefaults();

        // When createOne is called
        $model = $factory->createOne();
        $actual = $model->to_array();

        // Then defaults should be assigned
        $this->assertSame($expected, $actual);

        return $model;
    }

    /**
     * @test
     * @depends createOne_NoAttributes_ShouldCreateWithDefaults
     */
    public function createOne_CreatedModelMustBeInstanceOfModel(object $model): void
    {
        $this->assertInstanceOf(Model::class, $model);
    }

    /** @test */
    public function createOne_PersistenceStrategySet_ShouldPersistGeneratedModels(): void
    {
        // Given factory
        $factory = $this->getFactory();

        // And persistence strategy is set
        $persistence = $this->createMock(PersistenceContract::class);
        $persistence->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function (Model ...$models) {
            });

        $factory->setPersistence($persistence);

        // When createOne is called
        // Then persistence should be called once
        $factory->createOne();
    }

    /** @test */
    public function createOne_AttributesGiven_ShouldMergeWithDefaults(): Model
    {
        // Given factory
        $factory = $this->getFactory();
        $factory->getDefaults();
        $attributes = ['id' => 123, 'field' => 'field'];
        $expected = [
            'id' => 123,
            'status' => 'status',
            'body' => 'body',
            'relation' => null,
            'field' => 'field'
        ];

        // When createOne is called
        $model = $factory->createOne($attributes);
        $actual = $model->to_array();

        // Then defaults should be overwritten by given attributes
        $this->assertSame($expected, $actual);

        return $model;
    }

    /**
     * @test
     * @return array{FactoryContract, array<Model>}
     */
    public function createMany_NoAttributes_ShouldCreateWithDefaults(): array
    {
        // Given factory
        $factory = $this->getFactory();
        $expected = $factory->getDefaults();

        // When createMany is called
        $models = $factory->createMany();

        foreach ($models as $model) {
            $actual = $model->to_array();

            // Then defaults should be assigned to all created models
            $this->assertSame($expected, $actual);
        }

        return [$factory, $models];
    }

    /**
     * @test
     * @param array{FactoryContract, array<Model>} $data
     * @depends createMany_NoAttributes_ShouldCreateWithDefaults
     */
    public function createMany_NoCountPassed_ShouldCreateFiveModels(array $data): void
    {
        // Given models generated without count param
        [, $models] = $data;
        $expected = 5;
        // Then count should be = 5
        $this->assertCount($expected, $models);
    }

    /**
     * @test
     * @param array{FactoryContract, array<Model>} $data
     * @depends createMany_NoAttributes_ShouldCreateWithDefaults
     */
    public function createMany_EachMustBeInstanceOfModel(array $data): void
    {
        // Given models generated by createMany
        [, $models] = $data;

        // Then each model must be
        foreach ($models as $model) {
            $this->assertInstanceOf(Model::class, $model);
        }
    }

    /**
     * @test
     * @param array{FactoryContract, array<Model>} $data
     * @depends createMany_NoAttributes_ShouldCreateWithDefaults
     */
    public function createMany_CountPassed_ShouldCreateGivenCountOfModels(array $data): void
    {
        // Given models generated without count param
        [$factory] = $data;
        $expected = 2;

        // When created
        $models = $factory->createMany([], $expected);

        // Then count should be = 2
        $this->assertCount($expected, $models);
    }

    /** @test */
    public function with_NotExistingState_WillThrowOutOfBoundException(): void
    {
        // Expects
        $this->expectException(OutOfStateBound::class);
        $this->expectExceptionMessage("Attempted to reach 'not existing' but it does not exists");

        // Given factory
        $factory = $this->getFactory();

        // When with not exiting state called
        $factory->with('not existing')->makeOne();
    }

    /** @test */
    public function with_State_WillIncrementCount(): void
    {
        // Given factory with count = 0
        $factory = $this->getFactory();

        $existingState = 'fake';

        // When called with state
        $factory->with($existingState)->makeOne();
        $afterCallCount = count($factory);

        // Then call should be 1
        $this->assertEquals(1, $afterCallCount);
    }

    /** @test */
    public function with_StateAsObject_WillThreatItAsString(): void
    {
        // Given factory
        $factory = $this->getFactory();

        // When called with fake state as object
        $factory->with(State::for('fake', []));

        // Then state should be applied
        $this->assertCount(1, $factory);
    }

    /** @test */
    public function withIdsFor_ShouldIncrementCount(): FactoryContract
    {
        // Given factory
        $factory = $this->getFactory();
        $initialStateCount = count($factory);

        $factoryClone = clone $factory;

        // When called withIdsFor
        $factory->withIdsFor('id');

        // Then count of states should be
        $expectedCount = $initialStateCount + 1;
        $this->assertCount($expectedCount, $factory);

        return $factoryClone;
    }

    /**
     * @test
     * @depends withIdsFor_ShouldIncrementCount
     */
    public function withIdsFor_ShouldReplaceGivenIdsByRandomNumber(FactoryContract $factory): void
    {
        // Given factory with some predefined attributes
        $beforeAttributes = ['id' => -1, 'status' => 'status', 'body' => 'body', 'related_id' => null];
        $beforeIdsAddedModel = $factory->makeOne($beforeAttributes)->to_array();

        // And expected attrs set
        // When after withIdsFor is called
        $afterIdsAddedModel = $factory->withIdsFor('id', 'related_id')->makeOne($beforeAttributes)->to_array();

        // Then id and related_id fields should be replaced
        // by random number from faker
        $this->assertNotSame($beforeIdsAddedModel['id'], $afterIdsAddedModel['id']);
        $this->assertSame($beforeIdsAddedModel['status'], $afterIdsAddedModel['status']);
        $this->assertSame($beforeIdsAddedModel['body'], $afterIdsAddedModel['body']);
        $this->assertNotSame($beforeIdsAddedModel['related_id'], $afterIdsAddedModel['related_id']);
        $this->assertIsNumeric($afterIdsAddedModel['related_id']);
    }

    /** @test */
    public function withIds_withIdsForCalled_returnsTrue(): void
    {
        // Given factory
        $factory = $this->getFactory();

        // When called withIdsFor "id" field
        $factory->withIdsFor('id');

        // Then withIds method should return true
        $this->assertTrue($factory->withIds());
    }

    /** @test */
    public function withIds_withIdsForNotCalled_returnsFalse(): void
    {
        // Given factory
        $factory = $this->getFactory();

        // When withIdsFor method is not called

        // Then withIds method should return false
        $this->assertFalse($factory->withIds());
    }

    /** @test */
    public function with_stateAsArrayReference_callsRelatedFactoryWithDefaultState(): void
    {
        // Given factory
        $factory = $this->getFactory();

        // When with factory (relation reference) states called makeOne
        $factory->with('factory');
        /** @var ModelImitation $model */
        $model = $factory->makeOne();

        // Then nested relation should be called with default values
        $this->assertNotEmpty($model->relation);
        $this->assertInstanceOf(ModelImitation::class, $model->relation);
        $this->assertSame('body', $model->relation->body);
    }
}
