using System;
using System.IO;
using System.Collections.Generic;
using System.Text;
using System.Reflection;
using System.Linq;
using System.Diagnostics;


namespace MorfParsingLibrary
{
    /// <summary>
    /// Wowowow...This class is awesome!
    /// </summary>
    public class MorfParser
    {
        ImprovedAffixHandler iah;//разбиение слова на корень и аффиксы
        FeaturesDeterminers fd;//определение характеристик
        //Значения по умолчанию
        #region Default values
        string dictionary_path = Path.GetDirectoryName(Assembly.GetEntryAssembly().Location) + @"\DICT.txt";
        string affixes_path = Path.GetDirectoryName(Assembly.GetEntryAssembly().Location) + @"\Affixes.txt";
        string rules_path = Path.GetDirectoryName(Assembly.GetEntryAssembly().Location) + @"\Rules.txt";
        string exceptions_path = Path.GetDirectoryName(Assembly.GetEntryAssembly().Location) + @"\Exceptions.txt";
        string affinfo_path = Path.GetDirectoryName(Assembly.GetEntryAssembly().Location) + @"\AffInfo.txt";

        string InputText_Path = Path.GetDirectoryName(Assembly.GetEntryAssembly().Location) + @"\Input.txt";
        string OutputText_Path = Path.GetDirectoryName(Assembly.GetEntryAssembly().Location) + @"\Output.txt";

        internal static string[] Dictionary, Affixes, Rules, Exceptions, AffInfo;
        #endregion
        //Характеристики слова
        #region WordFeatures
        public string Word { get; private set; }
        public List<string> ChastRechi { get; private set; }
        public List<string> Root { get; private set; }
        public List<string> Affix { get; private set; }
        public List<string> Vremya { get; private set; }
        public List<string> Padezh { get; private set; }
        public List<string> PluralOrNot { get; private set; }
        public List<string> Face { get; private set; }
        public List<string> Negativ { get; private set; }
        public List<string> Infinitiv { get; private set; }
        public List<string> AffixInfo { get; private set; }//на 1 меньше, потому что не исп. в PutInFile

        List<KeyValuePair<string, string>> RootAffixesCollection;
        //кол-во "значений" слова
        public int COUNT { get; private set; }
        #endregion
        //Начальная нициализация компонентов
        public void InitializeComponents()
        {
            RootAffixesCollection = new List<KeyValuePair<string, string>>();
            ChastRechi = new List<string>();
            Root = new List<string>();
            Affix = new List<string>();
            Vremya = new List<string>();
            Padezh = new List<string>();
            PluralOrNot = new List<string>();
            Face = new List<string>();
            Negativ = new List<string>();
            Infinitiv = new List<string>();
            AffixInfo = new List<string>();

            COUNT = 0;

        }
        //public конструкторы, свойства, методы
        #region Public methods/properties

        public MorfParser()
        {
            InitializeComponents();

            Dictionary = File.ReadAllLines(dictionary_path);
            Affixes = File.ReadAllLines(affixes_path);
            Rules = File.ReadAllLines(rules_path);
            Exceptions = File.ReadAllLines(exceptions_path);
            AffInfo = File.ReadAllLines(affinfo_path);

            Helper.GetNumbersOfLines(Dictionary);

            fd = new FeaturesDeterminers();

            // Create a file for output named TestFile.txt.
            Stream myFile = File.Create(Path.GetDirectoryName(Assembly.GetEntryAssembly().Location) + @"\TestFile.txt");

            /* Create a new text writer using the output stream, and add it to
             * the trace listeners. */
            TextWriterTraceListener myTextListener = new TextWriterTraceListener(myFile);
            Trace.Listeners.Add(myTextListener);
            Trace.AutoFlush = true;
        }
        //копи конструктор
        private MorfParser(MorfParser other)
        {
            this.Word = other.Word;
            this.ChastRechi = other.ChastRechi;
            this.Root = other.Root;
            this.Affix = other.Affix;
            this.Vremya = other.Vremya;
            this.Padezh = other.Padezh;
            this.PluralOrNot = other.PluralOrNot;
            this.Face = other.Face;
            this.Negativ = other.Negativ;
            this.Infinitiv = other.Infinitiv;
            this.AffixInfo = other.AffixInfo;

            this.COUNT = other.COUNT;
        }
        //когда слово неизвестное
        private MorfParser(string word)
        {
            InitializeComponents();

            this.Word = word;
            this.ChastRechi.Add(Constants.UNKNOWN);
            this.Root.Add(Constants.UNKNOWN);
            this.Affix.Add(Constants.UNKNOWN);
            this.Vremya.Add(Constants.UNKNOWN);
            this.Padezh.Add(Constants.UNKNOWN);
            this.PluralOrNot.Add(Constants.UNKNOWN);
            this.Face.Add(Constants.UNKNOWN);
            this.Negativ.Add(Constants.UNKNOWN);
            this.Infinitiv.Add(Constants.UNKNOWN);
            this.AffixInfo.Add(Constants.UNKNOWN);

            this.COUNT = 1;
        }

        //Анализ слов из файла
        public void DealWithTextFromFile(string InputPath)
        {
            Trace.WriteLine("НАЧАЛО. " + DateTime.Now + "\r\n");
            string readText = File.ReadAllText(InputPath, Encoding.Default);
            string[] word_array = readText.ToLower().Split(Helper.separator, StringSplitOptions.RemoveEmptyEntries);
            word_array = OnlyDistinct(word_array);
            foreach (string word in word_array)
            {
                Trace.WriteLine("На вход поступило слово: " + word + "\r\n");
                SearchInDictionaries(word);
                DetermineOnYourOwn(word);
                for (int i = 0; i < COUNT; i++)
                {
                    PutInFile(OutputText_Path, Word, ChastRechi[i], PluralOrNot[i], Vremya[i], Padezh[i], Root[i], Affix[i], Face[i], Negativ[i], Infinitiv[i]);
                }
                InitializeComponents();
            }
            Trace.WriteLine("КОНЕЦ. " + DateTime.Now + "\r\n");
            
            
        }
        //Анализ слов, введенных вручную
        public List<MorfParser> DealWithManualText(string InputText)
        {
            Trace.WriteLine("НАЧАЛО. " + DateTime.Now + "\r\n");
            List<MorfParser> lmp = new List<MorfParser>();

            string[] word_array = InputText.ToLower().Split(Helper.separator, StringSplitOptions.RemoveEmptyEntries);
            word_array = OnlyDistinct(word_array);
            foreach (string word in word_array)
            {
                Trace.WriteLine("На вход поступило слово: " + word + "\r\n");

                SearchInDictionaries(word);
                DetermineOnYourOwn(word);

                if (ChastRechi.Count == 0) lmp.Add(new MorfParser(Word));
                else lmp.Add(new MorfParser(this));
                InitializeComponents();
            }
            Trace.WriteLine("\r\nКОНЕЦ. " + DateTime.Now + "\r\n");
            return lmp;
        }

        //путь к словарю
        public string DictionaryPath
        {
            get
            {
                return dictionary_path;
            }
            set
            {
                dictionary_path = value;
                Dictionary = File.ReadAllLines(dictionary_path);
                Helper.GetNumbersOfLines(Dictionary);
            }
        }
        //путь к аффиксам
        public string AffixesPath
        {
            get
            {
                return affixes_path;
            }
            set
            {
                affixes_path = value;
                Affixes = File.ReadAllLines(affixes_path);
            }
        }
        //путь к правилам
        public string RulesPath
        {
            get
            {
                return rules_path;
            }
            set
            {
                rules_path = value;
                Rules = File.ReadAllLines(rules_path);
            }
        }
        //путь к исключениям
        public string ExceptionsPath
        {
            get
            {
                return exceptions_path;
            }
            set
            {
                exceptions_path = value;
                Exceptions = File.ReadAllLines(exceptions_path);
            }
        }
        //путь к аффинфу
        public string AffInfoPath
        {
            get
            {
                return affinfo_path;
            }
            set
            {
                affinfo_path = value;
                AffInfo = File.ReadAllLines(affinfo_path);
            }
        }
        //путь к исходному файлу
        public string InputPath
        {
            get
            {
                return InputText_Path;
            }
            set
            {
                InputText_Path = value;
            }
        }
        public string OutputPath
        {
            get
            {
                return OutputText_Path;
            }
            set
            {
                OutputText_Path = value;
            }
        }
        #endregion
        //Функции определения характеристик слов
        #region ОПРЕДЕЛЕНИЕ ХАРАКТЕРИСТИК СЛОВ
        ///ХАРАКТЕРИСТИКИ СЛОВ
        //КОРНИ И АФФИКСЫ (коллекция)
        internal void AddtoRootAffixCollection(string root, string affix)
        {
            RootAffixesCollection.Add(new KeyValuePair<string, string>(root, affix));
        }
        //ЧАСТЬ РЕЧИ
        private List<string> ChastRechiDeterminer(string word)
        {

            int begin, end;
            Helper.getCurrentNumbers(word, out begin, out end);
            List<String> results = new List<string>();
            iah = new ImprovedAffixHandler(results, this, begin, end);
            iah.MainWordDivider(word, "");
            return results;
        }
        //ЧИСЛО
        private string PluralOrNotDeterminer(string word, string chastrechi, string affixes)
        {
            string[] aff_massiv = affixes.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);
            string PLURAL = Constants.UNKNOWN;

            switch (chastrechi)
            {
                case Constants.NOUN:
                case Constants.ADJECTIVE:
                case Constants.NUMERIC:
                    PLURAL = fd.DeterminePluralOfSusch(word, aff_massiv);
                    break;
                case Constants.PRICHASTIE:
                    PLURAL = fd.DeterminePluralOfPrichastie(word, aff_massiv);
                    break;
                case Constants.PRONOUN:
                    PLURAL = fd.DeterminePluralOfPron(word, aff_massiv);
                    break;
                case Constants.VERB:
                    PLURAL = fd.DeterminePluralOfVerb(word, aff_massiv);
                    break;
            }
            

            return PLURAL;
        }
        //ВРЕМЯ
        private string VremyaDeterminer(string word, string chastrechi, string affixes)
        {
            string[] aff_massiv = affixes.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);
            string VREMYA = Constants.UNKNOWN;

            switch (chastrechi)
            {
                case Constants.VERB:
                    VREMYA = fd.DetermineVremyaOfGlagol(word, aff_massiv);
                    break;
                case Constants.PRICHASTIE:
                    VREMYA = fd.DetermineVremyaOfPichastie(word, aff_massiv);
                    break;
                default:
                    VREMYA = fd.DetermineVremyaOfOstalnoe(word, aff_massiv);
                    break;
            }
            return VREMYA;
        }
        //ПАДЕЖ
        private string PadezhDeterminer(string word, string chastrechi, string affixes)
        {
            string[] aff_massiv = affixes.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);

            string PADEZH = Constants.UNKNOWN;

            switch (chastrechi)
            {
                case Constants.NOUN:
                case Constants.ADJECTIVE:
                case Constants.NUMERIC:
                case Constants.PRONOUN:
                    PADEZH = fd.DeterminePadezhOfSusch(word, aff_massiv);
                    break;
                case Constants.PRICHASTIE:
                    PADEZH = fd.DeterminePadezhOfPrichastie(word, aff_massiv);
                    break;
                default:
                    break;
            }
            
            return PADEZH;

        }
        //ЛИЦО
        private string FaceDeterminer(string word, string chastrechi, string affixes)
        {
            string[] aff_massiv = affixes.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);

            string FACE = Constants.UNKNOWN;
            switch (chastrechi)
	        {
                case Constants.NOUN:
                    FACE = fd.DetermineFaceOfSusch(word, aff_massiv);                    
                    break;
                case Constants.PRICHASTIE:
                    FACE = fd.DetermineFaceOfPrichastie(word, aff_massiv);
                    break;
                case Constants.VERB:
                    FACE = fd.DetermineFaceOfGlagol(word, aff_massiv);
                    break;
                case Constants.NUMERIC:
                    FACE = fd.DetermineFaceOfChislitelnoe(word, aff_massiv); 
                    break;
                case Constants.PRONOUN:
                    FACE = fd.DetermineFaceOfPronoun(word, aff_massiv);
                    break;
	        }

             
            return FACE;
        }
        //НЕГАТИВ
        private string NegativDeterminer(string word, string chastrechi, string affixes)
        {
            string[] aff_massiv = affixes.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);
            string res = Constants.POSITIVE;
            switch (chastrechi)
            {
                case Constants.VERB:
                    res = fd.DetermineNegativeOfGlagol(word, aff_massiv);
                    break;
            }
            return res;
        }
        //ИНФИНИТИВ
        private string InfinitivDeterminer(string word, string chastrechi, string affixes)
        {
            string[] aff_massiv = affixes.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);
            string res = Constants.NULL;
            switch (chastrechi)
            {
                case Constants.VERB:
                    res = fd.DetermineInfinitivOfGlagol(word, aff_massiv);
                    break;
            }
            return res;
        }
        //ИНФО ОБ АФФИКСАХ
        private string AffixInfoDeterminer(string word, string chastrechi, string affixes)
        {
            string[] aff_massiv = affixes.Split(new char[] { '|' }, StringSplitOptions.RemoveEmptyEntries);
            string res = Constants.NULL;
            switch (chastrechi)
            {
                default:
                    res = fd.DetermineAffixInfo(word, chastrechi, aff_massiv);
                    break;
            }
            return res;
            
        }
        #endregion
        //главная работа, поиск, анализ слов
        #region MAIN WORK
        //определение по алгориту
        private void DetermineOnYourOwn(string word)
        {

            string aff = "";
            string temp_w = Helper.loseEnds(word, out aff);
            temp_w = Helper.TransformSomeWords(temp_w);

            List<string> temp_ChastRechi = ChastRechiDeterminer(temp_w);//часть речи

            COUNT += temp_ChastRechi.Count;
            for (int i = 0; i < temp_ChastRechi.Count; i++)
            {
                Word = word;
                ChastRechi.Add(temp_ChastRechi[i]);
                Root.Add(RootAffixesCollection[i].Key); //корень
                Affix.Add(RootAffixesCollection[i].Value + aff); //аффиксы

                PluralOrNot.Add(PluralOrNotDeterminer(Root[Root.Count - 1], temp_ChastRechi[i], Affix[Affix.Count - 1])); //число
                Vremya.Add(VremyaDeterminer(Root[Root.Count - 1], temp_ChastRechi[i], Affix[Affix.Count - 1])); //время
                Padezh.Add(PadezhDeterminer(Root[Root.Count - 1], temp_ChastRechi[i], Affix[Affix.Count - 1])); //падеж                
                Face.Add(FaceDeterminer(Root[Root.Count - 1], temp_ChastRechi[i], Affix[Affix.Count - 1]));//лицо       

                Negativ.Add(NegativDeterminer(Root[Root.Count - 1], temp_ChastRechi[i], Affix[Affix.Count - 1])); //негативность глагола
                Infinitiv.Add(InfinitivDeterminer(Root[Root.Count - 1], temp_ChastRechi[i], Affix[Affix.Count - 1])); //инфитивность
                AffixInfo.Add(AffixInfoDeterminer(Root[Root.Count - 1], temp_ChastRechi[i], Affix[Affix.Count - 1])); //инфа об аффиксах
            }
        }
        //определение по словарю
        private void SearchInDictionaries(string word)
        {
            string aff;
            //слово без окончания
            string temp_w = Helper.loseEnds(word, out aff);
            Word = word;

            int begin, end;
            Helper.getCurrentNumbers(temp_w, out begin, out end);
            
            //поиск слова в осн.словаре
            for (int i = begin; i < end; i++ )
            {
                string[] seek_arr = Dictionary[i].Split(new char[] { ',' });
                if (temp_w == seek_arr[0])
                {
                    bool found = false;
                    foreach (string line in Helper.defaults)
                    {                        
                        string[] mas = line.Split(new char[] { ',' }, StringSplitOptions.RemoveEmptyEntries);
                        if (mas[0] == seek_arr[1])
                        {
                            if (seek_arr[1] == "pron")//////////////ПЛОХО////////////////
                            {
                                for (int j = 0; j < Helper.pron_defaults.Length; j++)
                                {
                                    string[] mas_pron = Helper.pron_defaults[j].Split(new char[] { ',' }, StringSplitOptions.RemoveEmptyEntries);
                                    if (seek_arr[0] == mas_pron[0])
                                    {
                                        ChastRechi.Add(Constants.PRONOUN);
                                        PluralOrNot.Add(mas_pron[1]);
                                        Vremya.Add(Constants.NULL);
                                        Padezh.Add(mas_pron[2]);
                                        Root.Add(temp_w);
                                        Affix.Add(aff);
                                        Face.Add(mas_pron[3]);
                                        Negativ.Add(Constants.POSITIVE);
                                        Infinitiv.Add(Constants.NULL);
                                        AffixInfo.Add(Constants.NULL);

                                        found = true;

                                        Trace.WriteLine("Найдено совпадение в словаре: " + temp_w + " " + mas[1]);
                                        break;
                                    }
                                }
                            }
                            else
                            {
                                ChastRechi.Add(mas[1]);
                                PluralOrNot.Add(mas[2]);
                                Vremya.Add(mas[3]);
                                Padezh.Add(mas[4]);
                                Root.Add(temp_w);
                                Affix.Add(aff);
                                Face.Add(mas[5]);
                                Negativ.Add(mas[6]);
                                Infinitiv.Add(mas[7]);
                                AffixInfo.Add(Constants.NULL);

                                found = true;

                                Trace.WriteLine("Найдено совпадение в словаре: " + temp_w + " " + mas[1]);
                                break;
                            }
                        }
                       
                    }
                    if (!found)
                    {
                        ChastRechi.Add(Helper.convertPartOfSpeech(seek_arr[1]));
                        PluralOrNot.Add(Constants.NULL);
                        Vremya.Add(Constants.NULL);
                        Padezh.Add(Constants.NULL);
                        Root.Add(temp_w);
                        Affix.Add(aff);
                        Face.Add(Constants.NULL);
                        Negativ.Add(Constants.POSITIVE);
                        Infinitiv.Add(Constants.NULL);
                        AffixInfo.Add(Constants.NULL);

                        Trace.WriteLine("Найдено совпадение в словаре: " + temp_w + "-" + Helper.convertPartOfSpeech(seek_arr[1]));
                    }
                    COUNT++;
                    
                }
            }

        }
        #endregion
        //Help методы [добавить затем в отдельный класс]
        #region HelpMethods
        //Запись в файл проанализированных слов
        private void PutInFile(string destination_path, string word, string chastrechi, string pluralornot, string vremya, string padezh, string root, string affix, string face, string neg, string infinitiv)
        {
            bool state = false;

            if (!File.Exists(destination_path))
            {
                using (FileStream fs = File.Create(destination_path))
                {
                }
            }

            string new_slovar_line =
                word + " " + chastrechi + " " + pluralornot + " " + vremya + " " + padezh + " " + root + " " + affix + " " + face + " " + neg + " " + infinitiv;
            foreach (string check_line in File.ReadLines(destination_path))
            {
                if (check_line == new_slovar_line)
                {
                    state = true;
                    break;
                }
            }
            if (!state)
            {
                File.AppendAllText(destination_path, new_slovar_line + "\r\n", Encoding.UTF8);
            }
            

        }
        //отделение повторяющихся слов
        private string[] OnlyDistinct(string[] word_array)
        {
            HashSet<string> hs = new HashSet<string>();

            for (int i = 0; i < word_array.Length; i++)
            {
                hs.Add(word_array[i]);
            }
            hs.Remove("\r");
            return hs.ToArray<string>();
        }
        #endregion

    }
}
