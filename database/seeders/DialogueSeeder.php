<?php

namespace Database\Seeders;

use App\Models\Dialogue;
use App\Models\DialogueResult;
use App\Models\Message;
use App\Models\MessageSender;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DialogueSeeder extends Seeder
{
    /** @var Collection<string, int> */
    private Collection $resultIds;

    /** @var Collection<string, int> */
    private Collection $senderIds;

    public function run(): void
    {
        $this->resultIds = DialogueResult::query()->pluck('id', 'slug');
        $this->senderIds = MessageSender::query()->pluck('id', 'slug');

        $this->createDialogue(
            managerEmail: 'anna@example.com',
            clientEmail: 'igor@example.com',
            resultSlug: 'bought',
            messages: [
                [
                    'client',
                    '2026-08-10 10:02:00',
                    'Добрый день! Интересует тариф «Бизнес» для команды из 8 человек.',
                ],
                [
                    'manager',
                    '2026-08-10 10:04:00',
                    'Здравствуйте, Игорь! Подскажите, какие задачи планируете решать в первую очередь?',
                ],
                [
                    'client',
                    '2026-08-10 10:06:00',
                    'Нужна аналитика диалогов и контроль качества общения менеджеров.',
                ],
                [
                    'manager',
                    '2026-08-10 10:08:00',
                    'Отлично, тариф «Бизнес» как раз закрывает эти задачи. Могу подключить демо-доступ на 7 дней.',
                ],
                [
                    'client',
                    '2026-08-10 10:11:00',
                    'Хорошо, давайте оформим.',
                ],
                [
                    'manager',
                    '2026-08-10 10:13:00',
                    'Отправила счёт и инструкцию по подключению. Спасибо за доверие!',
                ],
            ],
        );

        $this->createDialogue(
            managerEmail: 'maxim@example.com',
            clientEmail: 'elena@example.com',
            resultSlug: 'not_bought',
            messages: [
                [
                    'client',
                    '2026-08-11 14:20:00',
                    'Сколько стоит ваш сервис для небольшого интернет-магазина?',
                ],
                [
                    'manager',
                    '2026-08-11 14:22:00',
                    'Елена, базовый тариф — 4 900 ₽ в месяц. Есть пробный период 14 дней.',
                ],
                [
                    'client',
                    '2026-08-11 14:25:00',
                    'Для нас это дороговато, мы только начинаем и бюджет ограничен.',
                ],
                [
                    'manager',
                    '2026-08-11 14:27:00',
                    'Понимаю. Могу предложить стартовый пакет со скидкой 20% на первые 3 месяца.',
                ],
                [
                    'client',
                    '2026-08-11 14:30:00',
                    'Спасибо, но пока откажемся. Вернусь, когда вырастем.',
                ],
            ],
        );

        $this->createDialogue(
            managerEmail: 'anna@example.com',
            clientEmail: 'dmitry@example.com',
            resultSlug: 'bought',
            messages: [
                [
                    'client',
                    '2026-08-12 09:15:00',
                    'У нас уже есть CRM, зачем нам ещё один сервис анализа?',
                ],
                [
                    'manager',
                    '2026-08-12 09:18:00',
                    'CRM хранит данные, а мы помогаем оценивать качество коммуникации и находить точки роста в продажах.',
                ],
                [
                    'client',
                    '2026-08-12 09:22:00',
                    'Звучит интересно, но интеграция обычно долгая и болезненная.',
                ],
                [
                    'manager',
                    '2026-08-12 09:25:00',
                    'Интеграция с вашей CRM занимает около 2 часов, подключим технического специалиста бесплатно.',
                ],
                [
                    'client',
                    '2026-08-12 09:30:00',
                    'Если так быстро — давайте попробуем на месяц.',
                ],
                [
                    'manager',
                    '2026-08-12 09:33:00',
                    'Отлично, оформляю доступ и назначаю созвон на завтра в 11:00.',
                ],
            ],
        );

        $this->createDialogue(
            managerEmail: 'sergey@example.com',
            clientEmail: 'olga@example.com',
            resultSlug: 'not_bought',
            messages: [
                [
                    'client',
                    '2026-08-13 16:40:00',
                    'Можете прислать коммерческое предложение?',
                ],
                [
                    'manager',
                    '2026-08-13 16:42:00',
                    'Конечно! Уточните, пожалуйста, сколько менеджеров будет работать в системе?',
                ],
                [
                    'client',
                    '2026-08-13 16:45:00',
                    'Примерно 15.',
                ],
                [
                    'manager',
                    '2026-08-13 16:47:00',
                    'Подготовлю КП в течение часа и отправлю на почту.',
                ],
                [
                    'client',
                    '2026-08-13 18:10:00',
                    'Получила. Нужно согласовать с руководством, отвечу позже.',
                ],
                [
                    'manager',
                    '2026-08-14 10:00:00',
                    'Ольга, добрый день! Удалось обсудить предложение с руководством?',
                ],
            ],
        );

        $this->createDialogue(
            managerEmail: 'maxim@example.com',
            clientEmail: 'artem@example.com',
            resultSlug: 'not_bought',
            messages: [
                [
                    'client',
                    '2026-08-14 11:05:00',
                    'Есть ли у вас API для выгрузки аналитики?',
                ],
                [
                    'manager',
                    '2026-08-14 11:07:00',
                    'Да, REST API доступен на тарифах «Бизнес» и «Enterprise».',
                ],
                [
                    'client',
                    '2026-08-14 11:10:00',
                    'Нам нужны кастомные отчёты, стандартные не подойдут.',
                ],
                [
                    'manager',
                    '2026-08-14 11:13:00',
                    'Можем обсудить кастомизацию на тарифе «Enterprise».',
                ],
                [
                    'client',
                    '2026-08-14 11:16:00',
                    'Пока не готов тратить время на внедрение, остановимся здесь.',
                ],
            ],
        );

        $this->createDialogue(
            managerEmail: 'sergey@example.com',
            clientEmail: 'marina@example.com',
            resultSlug: 'bought',
            messages: [
                [
                    'client',
                    '2026-08-15 13:00:00',
                    'Коллеги посоветовали ваш сервис. Чем вы лучше конкурентов?',
                ],
                [
                    'manager',
                    '2026-08-15 13:03:00',
                    'Мы автоматически классифицируем исходы диалогов и показываем, на каком этапе теряются клиенты.',
                ],
                [
                    'client',
                    '2026-08-15 13:06:00',
                    'Это как раз наша боль — много лидов, мало сделок.',
                ],
                [
                    'manager',
                    '2026-08-15 13:09:00',
                    'Предлагаю пилот на 2 недели с разбором 50 диалогов вашей команды.',
                ],
                [
                    'client',
                    '2026-08-15 13:12:00',
                    'Согласна, давайте запускать.',
                ],
            ],
        );
    }

    /**
     * @param  list<array{0: string, 1: string, 2: string}>  $messages
     */
    private function createDialogue(
        string $managerEmail,
        string $clientEmail,
        string $resultSlug,
        array $messages,
    ): void {
        $manager = User::query()->where('email', $managerEmail)->firstOrFail();
        $client = User::query()->where('email', $clientEmail)->firstOrFail();

        $dialogue = Dialogue::query()->create([
            'manager_id' => $manager->id,
            'client_id' => $client->id,
            'result_id' => $this->resultIds[$resultSlug],
        ]);

        foreach ($messages as [$senderSlug, $sentAt, $body]) {
            Message::query()->create([
                'dialogue_id' => $dialogue->id,
                'sender_id' => $this->senderIds[$senderSlug],
                'body' => $body,
                'sent_at' => Carbon::parse($sentAt),
            ]);
        }
    }
}
