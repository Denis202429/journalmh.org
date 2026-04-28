<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScrapedData;

class UpdateScrapedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:scraped-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update genre and category fields in ScrapedData based on type field';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $scrapedData = ScrapedData::all();

        // foreach ($scrapedData as $data) {
        //     //     switch ($data->type) {
        //     //         case 'Сăвă':
        //     //             $data->genre = 'Стихотворение';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Калав':
        //     //             $data->genre = 'Рассказ';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Юмах':
        //     //             $data->genre = 'Сказка';
        //     //             $data->category = 'Устное народное творчество';
        //     //             break;
        //     //         case 'Кулăшла калав':
        //     //             $data->genre = 'Юморезка';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Куçару':
        //     //             $data->genre = 'Не определен';
        //     //             $data->category = 'Не определена';
        //     //             break;
        //     //         case 'Юрă':
        //     //             $data->genre = 'Песня';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Шӳтле сăвă':
        //     //             $data->genre = 'Стихотворение';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Повесть':
        //     //             $data->genre = 'Повесть';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Юптару':
        //     //             $data->genre = 'Басня';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Очерк':
        //     //             $data->genre = 'Очерк';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Поэма':
        //     //             $data->genre = 'Поэма';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Халап':
        //     //             $data->genre = 'Не определен';
        //     //             $data->category = 'Не определена';
        //     //             break;
        //     //         case 'Прозăллă сăвă':
        //     //             $data->genre = 'Стихотворение';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Новелла':
        //     //             $data->genre = 'Новелла';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Роман':
        //     //             $data->genre = 'Роман';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Такмак':
        //     //             $data->genre = 'Частушка';
        //     //             $data->category = 'Устное народное творчество';
        //     //             break;
        //     //         case 'Сăвăллă юмах':
        //     //             $data->genre = 'Стихотворение';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Умсăмах':
        //     //             $data->genre = 'Предисловие';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Асаилӳ':
        //     //             $data->genre = 'Мемуарная и эпистолярная литература';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Çыру':
        //     //             $data->genre = 'Мемуарная и эпистолярная литература';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Пьеса':
        //     //             $data->genre = 'Пьеса';
        //     //             $data->category = 'Драматургия';
        //     //             break;
        //     //         case 'Баллада':
        //     //             $data->genre = 'Баллада';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Шăранчăк':
        //     //             $data->genre = 'Не определен';
        //     //             $data->category = 'Не определена';
        //     //             break;
        //     //         case 'Историллĕ роман':
        //     //             $data->genre = 'Роман';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Хыçсăмах':
        //     //             $data->genre = 'Не определен';
        //     //             $data->category = 'Не определена';
        //     //             break;
        //     //         case 'Сонет':
        //     //             $data->genre = 'Сонет';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Чăн пулни':
        //     //             $data->genre = 'Быль';
        //     //             $data->category = 'Устное народное творчество';
        //     //             break;
        //     //         case 'Эссе':
        //     //             $data->genre = 'Эссе';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Камит':
        //     //             $data->genre = 'Комедия';
        //     //             $data->category = 'Драматургия';
        //     //             break;
        //     //         case 'Мыскара':
        //     //             $data->genre = 'Комедия';
        //     //             $data->category = 'Драматургия';
        //     //             break;
        //     //         case 'Хаклав':
        //     //             $data->genre = 'Рецензия, отзыв';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Романс':
        //     //             $data->genre = 'Романс';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Монолог':
        //     //             $data->genre = 'Монолог';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Автобиографи':
        //     //             $data->genre = 'Автобиография';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Сонет кăшăлĕ':
        //     //             $data->genre = 'Сонет';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Триптих':
        //     //             $data->genre = 'Триптих';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Сценка':
        //     //             $data->genre = 'Сценка';
        //     //             $data->category = 'Драматургия';
        //     //             break;
        //     //         case 'Приключениллĕ повесть':
        //     //             $data->genre = 'Повесть';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Ăслăлăх фантастикин повеçĕ':
        //     //             $data->genre = 'Повесть';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Детективлă повесть':
        //     //             $data->genre = 'Повесть';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Ăслăлăх фантастикин калавĕ':
        //     //             $data->genre = 'Рассказ';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Фельетон':
        //     //             $data->genre = 'Фельетон';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Сăвăллă роман':
        //     //             $data->genre = 'Стихотворение';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Роман-халап':
        //     //             $data->genre = 'Роман';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Поэма-калав':
        //     //             $data->genre = 'Поэма';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Мифодрама':
        //     //             $data->genre = 'Драма';
        //     //             $data->category = 'Драматургия';
        //     //             break;
        //     //         case 'Акросăвă':
        //     //             $data->genre = 'Стихотворение';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Сăвăллă драма':
        //     //             $data->genre = 'Стихотворение';
        //     //             $data->category = 'Драматургия';
        //     //             break;
        //     //         case 'Инсценировка':
        //     //             $data->genre = 'Инсценировка';
        //     //             $data->category = 'Драматургия';
        //     //             break;
        //     //         case 'Трагеди':
        //     //             $data->genre = 'Трагедия';
        //     //             $data->category = 'Драматургия';
        //     //             break;
        //     //         case 'Сонет ярăмĕ':
        //     //             $data->genre = 'Сонет';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Поэма-юмах':
        //     //             $data->genre = 'Поэма';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Дневник':
        //     //             $data->genre = 'Мемуарная литература';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Рапсоди':
        //     //             $data->genre = 'Рапсодия';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Памфлет':
        //     //             $data->genre = 'Памфлет';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Сăвăллă повесть':
        //     //             $data->genre = 'Стихотворение';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Сăвăллă калав':
        //     //             $data->genre = 'Стихотворение';
        //     //             $data->category = 'Поэтические тексты';
        //     //             break;
        //     //         case 'Фантастикăлла калав':
        //     //             $data->genre = 'Рассказ';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Трагикомеди':
        //     //             $data->genre = 'Трагикомедия';
        //     //             $data->category = 'Драматургия';
        //     //             break;
        //     //         case 'Эскиз':
        //     //             $data->genre = 'Эскиз';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Ӳкерчĕк':
        //     //             $data->genre = 'Зарисовка';
        //     //             $data->category = 'Прозаические тексты';
        //     //             break;
        //     //         case 'Анекдот':
        //     //             $data->genre = 'Анекдот';
        //     //             $data->category = 'Устное народное творчество';
        //     //             break;

        //     //         default:
        //     //             $data->genre = 'Не определен';
        //     //             $data->category = 'Не определена';
        //     //             break;


        //     // Replace '::' with '-' in title
        //     if (strpos($data->title, '::') !== false) {
        //         $data->title = str_replace('::', '-', $data->title);
        //     }

        //     // Check content for word count
        //     if (str_word_count($data->content) <= 1) {
        //         $data->delete();
        //         $this->info("Deleted record ID: {$data->id} due to insufficient content");
        //     } else {
        //         $data->save();
        //         $this->info("Updated record ID: {$data->id} with genre: {$data->genre} and category: {$data->category}");
        //     }
        // }
        //     $data->save();
        //     $this->info("Updated record ID: {$data->id} with genre: {$data->genre} and category: {$data->category}");
        // }

        foreach ($scrapedData as $data) {
            // Replace '::' with '-' in title
            if (strpos($data->title, '::') !== false) {
                $data->title = str_replace('::', '-', $data->title);
            }

            // Check content for word count
            if (str_word_count($data->content) <= 1) {
                $data->delete();
                $this->info("Deleted record ID: {$data->id} due to insufficient content");
            } else {
                $data->save();
                $this->info("Updated record ID: {$data->id}");
            }
        }

        $this->info('Data successfully cleaned');
        return 0;
    }
}


//$ php artisan update:scraped-data