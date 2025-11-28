import { cn } from "cn-utility";

type ButtonSize = "sm" | "md" | "lg" | "full";

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  children: React.ReactNode;
  size?: ButtonSize;
  onClick: () => void;
  className?: string;
}

const Button = ({
  children,
  size = "sm",
  onClick,
  className,
  ...props
}: ButtonProps) => {
  return (
    <button
      className={cn(
        "bg-accent text-primary rounded-2xl px-9 py-3 flex items-center justify-center gap-2.5 cursor-pointer hover:bg-accent/80 transition-colors text-sm",
        {
          "w-32.5": size === "sm",
          "w-96": size === "md",
          "w-120": size === "lg",
          "w-full": size === "full",
        },

        className,
      )}
      onClick={onClick}
      {...props}
    >
      {children}
    </button>
  );
};

export default Button;
