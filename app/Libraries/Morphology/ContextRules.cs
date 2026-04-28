using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.IO;
using System.Diagnostics;

namespace MorfParsingLibrary
{
    /// <summary>
    /// Makes something important
    /// </summary>
    class ContextRules
    {
        //C-согласная
        //F-мягкая гласная
        //B-твердая гласная
        public static string B = "аӑуы";
        public static string C = "бвгджзйклмнпрсҫтфхцчшщ";
        public static string F = "еӗÿя";
        public static string WORD;
        public static string AFFUNIT;


        //поиск правила обработки контекстов
        public static void FindTemplate(string word, string AffUnit, string[] rules,ref string[] syllables, ref string chastrechi)
        {
            WORD = word;
            AFFUNIT = AffUnit;

            string lcon = word.Length > 1 ? word.Substring(word.Length - 2) : word; //получение лев контекста            
            string rcon = AffUnit.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries)[0]; //получение пр контекста

            foreach (string line in rules)
            {
                if (line.StartsWith("//")) continue; // пропуск комментариев
                string[] rule = line.Split(new char[] { ';' }); //читаем строку с правилом
                if (CheckLeftContext(lcon, rule[0]) && CheckRightContext(rcon, rule[1]))//если найдено подходящ правило
                {
                    //согласно правилу
                    WordModify(word, rule[2]); //слово
                    
                    syllables = ExtractSyllables(rule[3]);//символы восстановления
                    chastrechi = rule[4];//

                    Trace.WriteLine("Правило контекста: [" + WORD + " " + AFFUNIT + " | " + line + "]");
                    return;
                }

            }
            Trace.WriteLine("No Context Rule");

        }
        //символы по умолчанию
        public static string[] DefaultSyllables(string AffUnit)
        {
            string[] syll = null;
            switch (AffixInfoProvider.TypeOfRecovery(AffUnit))
            {
                case AffixInfoProvider.RecoveryMode.With:
                    syll = new string[] { "а", "е", "ӗ", "я" };
                    break;
                case AffixInfoProvider.RecoveryMode.Without:
                    syll = new string[] { "" };
                    break;
                case AffixInfoProvider.RecoveryMode.Both:
                    syll = new string[] { "а", "е", "ӗ", "я",  "" };//ӑ
                    break;
                    
            }
            return syll;
        }
        //проверка подходимости части речи
        public static bool CheckChastRechi(string chastrechi, string context_chastrechi)
        {
            if (context_chastrechi == null) return false;
            bool b = false;
            switch (context_chastrechi)
            {
                case "":
                    b = true;
                    break;
                default:
                    string[] m = context_chastrechi.Split(new char[] { ',' });
                    for (int i = 0; i < m.Length; i++)
                    {
                        string turned = Helper.convertPartOfSpeech(m[i]);
                        if (turned == chastrechi) 
                        {
                            b = true; 
                            break;
                        } 
                    }
                    break;
            }
            return b;
        }
        //формирование массива символов
        private static string[] ExtractSyllables(string p)
        {
            if (p == "") return new string[] { "" };
            string[] res = p.Split(new char[] { ',' }, StringSplitOptions.RemoveEmptyEntries);
            return res;
        }
        //редактирование аффикса 
        private static void AffixModify(string AffUnit, string p)
        {
            throw new NotImplementedException();
        }
        //редактирование слова согласно правилу
        private static void WordModify(string word, string p)
        {
            if (p == "") return;
            switch (p[0])
            {
                case '-':
                    WORD = word.Substring(0, word.Length - (int)Char.GetNumericValue(p[1]));
                    break;
                default:
                    break;
            }
            
        }
        //проверка лев контекста
        private static bool CheckLeftContext(string lcon_input, string lcon_rule)
        {
            bool compatible = true;
            if (lcon_rule != "")
            {
                if ((lcon_input != lcon_rule))
                {
                    if (!CompatibleLeft(lcon_input, lcon_rule))
                    {
                        compatible = false;
                    }
                }
            }
            return compatible;
        }
        //проверка пр контекста
        private static bool CheckRightContext(string rcon_input, string rcon_rule)
        {
            bool compatible = true;
            if (rcon_rule != "")
            {
                if (rcon_input != rcon_rule)
                {
                    if (!CompatibleRight(rcon_input, rcon_rule))
                    {
                        compatible = false;
                    }
                }
            }


            return compatible;
        }
        //проверка совместимости лев
        private static bool CompatibleLeft(string input, string rule)
        {

            string[] mas = rule.Split(new char []{','}, StringSplitOptions.RemoveEmptyEntries);
            for (int i = 0; i < mas.Length; i++)
            {
                if (input == mas[i])
                {
                    return true;
                }
            }


            string context = "";
            if (input.Length > 1)
            {
                if (C.Contains(input[0]))
                {
                    if (input[0] == input[1])
                    {
                        context = "2C";
                    }
                    else
                    {
                        context = "C" + input[1];
                    }
                } 
            }
            
            bool b = context == rule;
            return b;
        }
        //проверка совместимости прав
        private static bool CompatibleRight(string input, string rule)
        {
            string[] mas = rule.Split(new char[] { ',' }, StringSplitOptions.RemoveEmptyEntries);
            for (int i = 0; i < mas.Length; i++)
            {
                if (input == mas[i])
                {
                    return true;
                }
            }

            string context = "";
            if (F.Contains(input[0]))
            {
                context = "F";
            }
            else if (B.Contains(input[0]))
            {
                context = "B";
            } 


            bool b = context == rule;
            return b;
        }




        //проверка тверд/мягкость слова& 
        //true - тверд
        internal static bool Consistency(string word)
        {
            char x = word[word.Length - 1];

            string Back = "аӑуыо";
            string Front = "еӗиÿэюя";
            string special = "ьъ";
            
            if (special.Contains(x)) return false;

            for (int i = word.Length - 1; i >= 0; i--)
            {
                if (Back.Contains(word[i]))
                {
                    //Trace.WriteLine(word[i]);
                    return true; //твердыня
                }
                    
                if (Front.Contains(word[i]))
                {
                    //Trace.WriteLine(word[i]);
                    return false; //мягкота
                }
                    
            }
            throw new NotImplementedException("WTF man?");
        }
        //оканчив. на согл. или нет
        internal static bool Soglasnaya(string word)
        {
            bool sogl = false;
            string S = "бвгджзйклмнпрсҫтфхцчшщ";
            string special = "ьъ";
            char x = word[word.Length - 1];
            
            sogl = (S.Contains(x) || (special.Contains(x))) ? true : false; //оканчивается ли слово на согл
            return sogl;
        }
        //проверка звонкости согласной буквы
        internal static bool Zvonko(char character)
        {
            string z = "гджзйлмнр";
            //string nez = "бвкптфхцчшщ";

            if (z.Contains(character)) return true;
            else return false;
            throw new NotImplementedException("WTF man?");
        }
    }
}
