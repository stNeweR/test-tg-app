<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Phone;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Resources\ModelResource;
use MoonShine\Decorations\Block;
use MoonShine\Fields\ID;
use MoonShine\Fields\Field;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Fields\Text;
use MoonShine\MoonShineUI;
use MoonShine\Fields\Switcher;
use MoonShine\MoonShineRequest;
use Log;

/**
 * @extends ModelResource<User>
 */
class UserResource extends ModelResource
{
    protected string $model = User::class;

    protected string $title = 'Пользователи';

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [
            Block::make([
                ID::make()->sortable(),
                Text::make('Имя пользователя в тг', 'user_name'),
                Text::make('ID пользователя в тг', 'telegram_id'),
                Phone::make('Номер телефона', 'phone_number'),
                Switcher::make('Активен/или нет', 'phone_verified')
            ]),
        ];
    }

    public function detailFields(): array 
    {
        return [
            Block::make([
                ID::make()->sortable(),
                Text::make('Имя пользователя в тг', 'user_name'),
                Text::make('ID пользователя в тг', 'telegram_id'),
                Phone::make('Номер телефона', 'phone_number'),
                Checkbox::make('Активен/или нет', 'phone_verified'),
                HasMany::make('Все сообщения','messages', 'message', new MessageResource() )
            ]),

        ];
    } 

    
    public function indexButtons(): array
    {
        return [
            ActionButton::make('Toogle verify')
                ->method(
                    'updateSomething',
                    fn($item) => [
                        'resourceId' => $item->getKey(),
                        'new' => $item->getResource
                    ]
                )
        ];
    }

    public function updateSomething(MoonShineRequest $request)
    {
        // Log::info(json_encode($request->get('resourceId'), JSON_UNESCAPED_UNICODE));
        $id = $request->get('resourceId');
        Log::info(json_encode($id, JSON_UNESCAPED_UNICODE));
        $request->getResource();
        
        $user = User::query()->find($id);

        Log::info(json_encode($user->phone_verified, JSON_UNESCAPED_UNICODE));
        $user->update([
            'phone_verified' => !$user->phone_verified
        ]);

        Log::info(json_encode($user->phone_verified, JSON_UNESCAPED_UNICODE));

        if ($user->phone_verified) {
            Telegraph::message('Здравствуйте. Теперь все ваши сообщения будут записывать в бд.')->send();
        } else {
            Telegraph::message('Здравствуйте. Теперь все ваши сообщения не будут записывать в бд.')->send();
        }     


        MoonShineUI::toast("Пользователь обновлен!", 'success');
        
        return back();
    }


    /**
     * @param User $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    public function rules(Model $item): array
    {
        return [];
    }
}
