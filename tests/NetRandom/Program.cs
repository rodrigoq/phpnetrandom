using System;
using System.Globalization;
using System.IO;
using System.Reflection;
using System.Threading;

namespace Random
{
	class Program
	{
		static void Main(string[] args)
		{
			try
			{
				Thread.CurrentThread.CurrentCulture = CultureInfo.InvariantCulture;

				var cmd = GetCommandLine(args);
				var rand = new Random(cmd.Seed);
				if (cmd.Type == "int")
				{
					for (int i = 0; i < cmd.Length; i++)
					{
						if (cmd.Min.HasValue == false && cmd.Max.HasValue == false)
							Console.WriteLine(rand.Next());
						else if (cmd.Min.HasValue == false && cmd.Max.HasValue)
							Console.WriteLine(rand.Next(cmd.Max.Value));
						else if (cmd.Min.HasValue && cmd.Max.HasValue)
							Console.WriteLine(rand.Next(cmd.Min.Value, cmd.Max.Value));
					}
				}
				else if (cmd.Type == "double")
				{
					for (int i = 0; i < cmd.Length; i++)
					{
						var num = rand.NextDouble();
						long bits = BitConverter.DoubleToInt64Bits(num);
						Console.WriteLine($"{num} {Convert.ToString(bits, 2)}");
					}
				}
				else if (cmd.Type == "byte")
				{
					var buffer = new byte[cmd.Length];
					rand.NextBytes(buffer);
					Console.WriteLine(BitConverter.ToString(buffer).Replace("-", "").ToLower());
				}
			}
			catch (Exception ex)
			{
				Console.WriteLine("Error:");
				Console.WriteLine(ex.Message);
				Environment.Exit(1);
			}
		}

		private static Cmd GetCommandLine(string[] args)
		{
			if (args.Length < 3)
			{
				var exe = Path.GetFileName(Assembly.GetExecutingAssembly().CodeBase);
				Console.WriteLine("Generates random numbers.");
				Console.WriteLine("Usage:");
				Console.WriteLine($"   {exe} seed length int|double|byte [max|min max]");
				Console.WriteLine("Example:  ");
				Console.WriteLine($"   {exe} 3232498 10 int 0 100");
				Console.WriteLine(" Genedrates 10 random integers from 0 to 100, seed 3232498.");
				Environment.Exit(0);
			}
			var cmd = new Cmd();
			cmd.Seed = int.Parse(args[0]);
			cmd.Length = int.Parse(args[1]);
			cmd.Type = args[2];

			if (args.Length == 4)
				cmd.Max = int.Parse(args[3]);
			else if (args.Length > 4)
			{
				cmd.Min = int.Parse(args[3]);
				cmd.Max = int.Parse(args[4]);
			}

			return cmd;
		}

		class Cmd
		{
			public int Seed { get; set; }
			public int Length { get; set; }
			public int? Max { get; set; }
			public int? Min { get; set; }
			public string Type { get; set; }
		}
	}
}
