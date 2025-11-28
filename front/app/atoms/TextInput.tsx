import { cn } from "cn-utility";

type TextInputVariant = "md" | "full";

interface TextInputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  className?: string;
  id: string;
  variant?: TextInputVariant;
}

const TextInput = ({
  className,
  type = "text",
  id,
  variant = "md",
  ...props
}: TextInputProps) => {
  return (
    <input
      type={type}
      id={id}
      className={cn(
        "text-secondary border-2 border-tertiary focus:outline-secondary rounded-xl p-2.5 placeholder:text-tertiary",
        {
          "w-59": variant === "md",
          "w-full": variant === "full",
        },
        className,
      )}
      {...props}
    />
  );
};

export default TextInput;
